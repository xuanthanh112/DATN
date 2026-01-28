<?php

namespace App\Services;

use App\Services\Interfaces\CartServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\Interfaces\ProductServiceInterface as ProductService;
use App\Repositories\Interfaces\ProductRepositoryInterface as ProductRepository;
use App\Repositories\Interfaces\PromotionRepositoryInterface as PromotionRepository;
use App\Repositories\Interfaces\OrderRepositoryInterface as OrderRepository;
use App\Repositories\Interfaces\ProductVariantRepositoryInterface  as ProductVariantRepository;
use Cart;
use App\Mail\OrderMail;
use App\Models\Promotion;

/**
 * Class AttributeCatalogueService
 * @package App\Services
 */
class CartService  implements CartServiceInterface
{

    protected $productRepository;
    protected $productVariantRepository;
    protected $promotionRepository;
    protected $orderRepository;
    protected $productService;

    public function __construct(
        ProductRepository $productRepository,
        ProductVariantRepository $productVariantRepository,
        PromotionRepository $promotionRepository,
        OrderRepository $orderRepository,
        ProductService $productService,
    ){
        $this->productRepository = $productRepository;
        $this->productVariantRepository = $productVariantRepository;
        $this->promotionRepository = $promotionRepository;
        $this->orderRepository = $orderRepository;
        $this->productService = $productService;
    }

    

    public function create($request, $language = 1){
        try{
            $payload = $request->input();
            $product = $this->productRepository->findById($payload['id'], ['*'], [
                'languages' => function($query) use ($language) {
                    $query->where('language_id',  $language);
                }
            ]);
            $data = [
                'id' => $product->id,
                'name' => $product->languages->first()->pivot->name,
                'qty' => $payload['quantity'],
            ];
            if(isset($payload['attribute_id']) && count($payload['attribute_id'])){
                $attributeId = sortAttributeId($payload['attribute_id']);
                $variant = $this->productVariantRepository->findVariant($attributeId, $product->id, $language);
                $variantPromotion = $this->promotionRepository->findPromotionByVariantUuid($variant->uuid);
                $variantPrice = getVariantPrice($variant, $variantPromotion);

                $data['id'] =  $product->id.'_'.$variant->uuid;
                $data['name'] = $product->languages->first()->pivot->name.' '.$variant->languages()->first()->pivot->name;
                $data['price'] = ($variantPrice['priceSale'] > 0) ? $variantPrice['priceSale'] : $variantPrice['price'];
                $data['options'] = [
                    'attribute' => $payload['attribute_id'],
                ];
            }else{
                $product = $this->productService->combineProductAndPromotion([$product->id], $product, true); 
                $price = getPrice($product);
                $data['price'] = ($price['priceSale'] > 0) ? $price['priceSale'] : $price['price'];
                // Lưu giá gốc vào options để sau này tính lại khuyến mại
                $data['options'] = [
                    'priceOriginal' => $product->price
                ];
            }

            Cart::instance('shopping')->add($data);

            return true;
        }catch(\Exception $e ){
            echo $e->getMessage().$e->getCode();die();
            return false;
        }
    }

    public function update($request){
        try{
            $payload = $request->input();
            Cart::instance('shopping')->update($payload['rowId'], $payload['qty']);
            $cartCaculate = $this->cartAndPromotion();
            $cartItem = Cart::instance('shopping')->get($payload['rowId']);
            $cartCaculate['cartItemSubTotal'] = $cartItem->qty * $cartItem->price;

            return $cartCaculate;
        }catch(\Exception $e ){
            echo $e->getMessage().$e->getCode();die();
            return false;
        }
    }

    public function delete($request){
        try{
            $payload = $request->input();
            Cart::instance('shopping')->remove($payload['rowId']);
            $cartCaculate = $this->cartAndPromotion();
            return $cartCaculate;
        }catch(\Exception $e ){
            echo $e->getMessage().$e->getCode();die();
            return false;
        }
    }


    public function order($request, $system){
        DB::beginTransaction();
        try{
            $payload = $this->request($request);
            $order = $this->orderRepository->create($payload, ['products']);
            if($order->id > 0){
                $this->createOrderProduct($payload, $order, $request);
                $this->mail($order, $system);
                Cart::instance('shopping')->destroy();
            }
            DB::commit();
            return [
                'order' => $order,
                'flag' => TRUE,
            ];
        }catch(\Exception $e ){
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();die();
            return [
                'order' => null,
                'flag' => false,
            ];
        }
    }

    private function mail($order, $sytem){
        // Kiểm tra email trước khi gửi
        $to = $order->email ?? null;
        $cc = $sytem['contact_email'] ?? null;
        
        // Nếu không có email người nhận thì bỏ qua gửi email
        if(empty($to)){
            return;
        }
        
        $carts = Cart::instance('shopping')->content();
        $carts = $this->remakeCart($carts);
        $cartCaculate = $this->cartAndPromotion();
        $cartPromotion = $this->cartPromotion($cartCaculate['cartTotal']);
        $data = [
            'order' => $order, 
            'carts' => $carts, 
            'cartCaculate' => $cartCaculate, 
            'cartPromotion' => $cartPromotion
        ];
        
        try {
            // Chỉ thêm cc nếu có giá trị
            if(!empty($cc)){
                \Mail::to($to)->cc($cc)->send(new OrderMail($data));
                Log::info('Order email sent successfully to: ' . $to . ' (CC: ' . $cc . ')');
            } else {
                \Mail::to($to)->send(new OrderMail($data));
                Log::info('Order email sent successfully to: ' . $to);
            }
        } catch (\Exception $e) {
            // Log lỗi nhưng không làm crash đơn hàng
            Log::error('Failed to send order email to ' . $to . ': ' . $e->getMessage());
        }

    }


    

    private function createOrderProduct($payload, $order, $request){
        $carts = Cart::instance('shopping')->content();
        $carts = $this->remakeCart($carts);
        $temp = [];
        if(!is_null($carts)){
            foreach($carts as $key => $val){
                $extract = explode('_', $val->id);
                $temp[] = [
                    'product_id' => $extract[0],
                    'uuid' => ($extract[1]) ?? null,
                    'name' => $val->name,
                    'qty' => $val->qty,
                    'price' => $val->price,
                    'priceOriginal' => $val->priceOriginal,
                    'option' => json_encode($val->options), 
                ];
            }     
        }
        $order->products()->sync($temp);
    }

    private function request($request){
        
        $cartCaculate = $this->reCaculateCart();
        $cartPromotion = $this->cartPromotion($cartCaculate['cartTotal']);

        $payload = $request->except(['_token', 'voucher', 'create']);
        $payload['code'] = time();
        $payload['cart'] = $cartCaculate;
        $payload['promotion']['discount'] = $cartPromotion['discount'] ?? '';
        $payload['promotion']['name'] = $cartPromotion['selectedPromotion']->name ?? '';
        $payload['promotion']['code'] = $cartPromotion['selectedPromotion']->code ?? '';
        $payload['promotion']['startDate'] = $cartPromotion['selectedPromotion']->startDate ?? '';
        $payload['promotion']['endDate'] = $cartPromotion['selectedPromotion']->endDate ?? '';
        $payload['confirm'] = 'pending';
        $payload['delivery'] = 'pending';
        $payload['payment'] = 'unpaid';
        
        // Add customer_id if logged in
        if(\Auth::guard('customer')->check()) {
            $payload['customer_id'] = \Auth::guard('customer')->id();
        }
        
        return $payload;
    }

    private function cartAndPromotion(){
        // Tính cart total với product promotion (đã được áp dụng trong reCaculateCart)
        $cartCaculate = $this->reCaculateCart();
        
        // Tính tổng giá trị giỏ hàng GỐC (chưa áp dụng bất kỳ khuyến mại nào)
        $carts = Cart::instance('shopping')->content();
        $originalTotal = 0;
        foreach($carts as $cart){
            $originalPrice = isset($cart->priceOriginal) ? $cart->priceOriginal : $cart->price;
            $originalTotal += $originalPrice * $cart->qty;
        }
        
        // Lấy thông tin 2 loại khuyến mại
        $productPromotions = $this->getAppliedProductPromotions();
        $productDiscount = $productPromotions['discount'];
        
        // Tính khuyến mại đơn hàng dựa trên tổng GỐC
        $orderPromotion = $this->cartPromotion($originalTotal);
        $orderDiscount = $orderPromotion['discount'];
        
        // SO SÁNH và chọn loại khuyến mại TỐT HƠN
        if($orderDiscount > $productDiscount){
            // Áp dụng khuyến mại đơn hàng
            $cartCaculate['cartTotal'] = $originalTotal - $orderDiscount;
            $cartCaculate['cartDiscount'] = $orderDiscount;
            $cartCaculate['promotionType'] = 'order';
        } else {
            // Áp dụng khuyến mại sản phẩm (đã tính sẵn trong cartTotal)
            $cartCaculate['cartDiscount'] = $productDiscount;
            $cartCaculate['promotionType'] = 'product';
        }

        return $cartCaculate;
    }

    public function reCaculateCart(){
        $carts = Cart::instance('shopping')->content();
        $total = 0;
        $totalItems = 0;
        
        // Lấy danh sách product IDs từ giỏ hàng
        $productIds = [];
        foreach($carts as $cart){
            $explode = explode('_', $cart->id);
            $productId = $explode[0];
            if(!in_array($productId, $productIds)){
                $productIds[] = $productId;
            }
        }
        
        // Lấy khuyến mại theo sản phẩm
        $productPromotions = [];
        $appliedPromotionIds = []; // Lưu IDs các promotion đã apply
        if(count($productIds) > 0){
            $promotions = $this->promotionRepository->findByProduct($productIds);
            foreach($promotions as $promotion){
                $productPromotions[$promotion->product_id] = $promotion;
            }
        }
        
        // Tính toán lại giá với khuyến mại
        foreach($carts as $cart){
            $explode = explode('_', $cart->id);
            $productId = $explode[0];
            $cartQty = $cart->qty;
            
            // Lấy giá gốc từ cart (đã được set trong remakeCart hoặc từ options)
            $originalPrice = isset($cart->priceOriginal) ? $cart->priceOriginal : 
                            (isset($cart->options['priceOriginal']) ? $cart->options['priceOriginal'] : $cart->price);
            $finalPrice = $originalPrice;
            
            // Kiểm tra và áp dụng khuyến mại theo sản phẩm
            if(isset($productPromotions[$productId])){
                $promotion = $productPromotions[$productId];
                
                // Kiểm tra điều kiện số lượng
                if(isset($promotion->discountInformation) && 
                   isset($promotion->discountInformation['info']) &&
                   isset($promotion->discountInformation['info']['quantity'])){
                    
                    // Lấy quantity - có thể là giá trị đơn hoặc mảng
                    $quantityValue = $promotion->discountInformation['info']['quantity'];
                    $minQuantity = is_array($quantityValue) ? (int)($quantityValue[0] ?? 0) : (int)$quantityValue;
                    
                    // Lấy maxQuantity - sử dụng convert_price để parse chuỗi có định dạng
                    $maxQuantityValue = $promotion->discountInformation['info']['maxQuantity'] ?? null;
                    if($maxQuantityValue){
                        if(is_array($maxQuantityValue)){
                            $maxQuantity = convert_price($maxQuantityValue[0]);
                        } else {
                            $maxQuantity = convert_price($maxQuantityValue);
                        }
                    } else {
                        $maxQuantity = null;
                    }
                    
                    // Nếu maxQuantity là 0 hoặc null thì không giới hạn
                    if($maxQuantity === 0 || $maxQuantity === null){
                        $maxQuantity = null;
                    }
                    
                    // Kiểm tra số lượng trong giỏ hàng có đủ điều kiện không
                    if($cartQty >= $minQuantity && ($maxQuantity === null || $cartQty <= $maxQuantity)){
                        $discountValue = $promotion->discountValue ?? 0;
                        $discountType = $promotion->discountType ?? 'cash';
                        $maxDiscountValue = $promotion->maxDiscountValue ?? 0;
                        
                        if($discountValue > 0){
                            if($discountType == 'cash'){
                                $discount = $discountValue;
                            } else if($discountType == 'percent'){
                                $discount = ($discountValue / 100) * $originalPrice;
                            }
                            
                            // Áp dụng giới hạn chiết khấu tối đa
                            if($maxDiscountValue > 0 && $discount > $maxDiscountValue){
                                $discount = $maxDiscountValue;
                            }
                            
                            $finalPrice = $originalPrice - $discount;
                            if($finalPrice < 0) $finalPrice = 0;
                            
                            // Lưu lại promotion ID đã áp dụng
                            if(!in_array($promotion->promotion_id, $appliedPromotionIds)){
                                $appliedPromotionIds[] = $promotion->promotion_id;
                            }
                        }
                    }
                }
            }
            
            // Cập nhật giá trong cart
            $cart->price = $finalPrice;
            
            $total = $total + $finalPrice * $cartQty;
            $totalItems = $totalItems + $cartQty;
        }
        
        return [
            'cartTotal' => $total,
            'cartTotalItems' => $totalItems,
            'appliedPromotionIds' => $appliedPromotionIds
        ];
    }



    public function remakeCart($carts){
        $cartId = $carts->pluck('id')->all();
        $temp = [];
        $objects = [];
        if(count($cartId)){
            foreach($cartId as $key => $val){
                $extract = explode('_', $val);
                if(count($extract) > 1){
                    $temp['variant'][] = $extract[1];
                }else{
                    $temp['product'][] = $extract[0];
                }
            }

            
            if(isset($temp['variant'])){
                $objects['variants'] = $this->productVariantRepository->findByCondition(
                    [], true, [], ['id', 'desc'], ['whereIn' => $temp['variant'], 'whereInField' => 'uuid']
                )->keyBy('uuid');
            }
            
            if(isset($temp['product'])){
                $objects['products'] = $this->productRepository->findByCondition(
                    [
                        config('apps.general.defaultPublish')
                    ], true, [], ['id', 'desc'], ['whereIn' => $temp['product'], 'whereInField' => 'id']
                )->keyBy('id');
            }
           

            foreach($carts as $keyCart => $cart){
                $explode = explode('_', $cart->id);
                $objectId = $explode[1] ?? $explode[0];
                if (isset($objects['variants'][$objectId])) {
                    $variantItem = $objects['variants'][$objectId];
                    $variantImage = explode(',' ,$variantItem->album)[0] ?? null;
                    $cart->setImage($variantImage)->setPriceOriginal($variantItem->price);
                } elseif (isset($objects['products'][$objectId])) {
                    $productItem = $objects['products'][$objectId];
                    $cart->setImage($productItem->image)->setPriceOriginal($productItem->price);

                }
            }

        }

        return $carts;
    }

    public function cartPromotion($cartTotal = 0){
        $maxDiscount = 0;
        $selectedPromotion = null;
        $promotions = $this->promotionRepository->getPromotionByCartTotal();
        if(!is_null($promotions) && count($promotions) > 0){
            foreach($promotions as $promotion){
                // Kiểm tra discountInformation có tồn tại và có key 'info' không
                if(!isset($promotion->discountInformation) || !isset($promotion->discountInformation['info'])){
                    continue;
                }
                
                $discount = $promotion->discountInformation['info'];
                $amountFrom = $discount['amountFrom'] ?? [];
                $amountTo = $discount['amountTo'] ?? [];
                $amountValue = $discount['amountValue'] ?? [];
                $amountType = $discount['amountType'] ?? [];

                // Kiểm tra tất cả mảng phải có cùng số lượng phần tử
                if(!empty($amountFrom) && 
                   count($amountFrom) == count($amountTo) && 
                   count($amountTo) == count($amountValue) &&
                   count($amountValue) == count($amountType)){
                    
                    $bestDiscount = 0;
                    $bestPromotion = null;
                    $bestRangeIndex = -1;
                    
                    // Tìm khoảng khuyến mại phù hợp nhất
                    for($i = 0; $i < count($amountFrom); $i++){
                        $currentAmountFrom = convert_price($amountFrom[$i]);
                        $currentAmountTo = convert_price($amountTo[$i]);
                        $currentAmountValue = convert_price($amountValue[$i]);
                        $currentAmountType = $amountType[$i];
                        
                        $currentDiscount = 0;
                        $isInRange = false;
                        
                        // Kiểm tra điều kiện: cartTotal phải >= amountFrom
                        if($cartTotal >= $currentAmountFrom){
                            // Nếu cartTotal <= amountTo: nằm trong khoảng
                            if($cartTotal <= $currentAmountTo){
                                $isInRange = true;
                            }
                            // Nếu cartTotal > amountTo: vượt quá khoảng, nhưng vẫn tính giá trị giảm giá
                            // (sẽ chọn khoảng có giá trị giảm giá cao nhất)
                            
                            if($currentAmountType == 'cash'){
                                $currentDiscount = $currentAmountValue;
                            }else if($currentAmountType == 'percent'){
                                $currentDiscount = ($currentAmountValue/100)*$cartTotal;
                            }
                            
                            // Ưu tiên: nếu nằm trong khoảng thì ưu tiên hơn
                            // Nếu cả hai đều trong khoảng hoặc cả hai đều ngoài khoảng, chọn giá trị giảm giá cao nhất
                            if($isInRange){
                                // Nếu đã có khoảng trong range tốt hơn, chỉ cập nhật nếu giá trị giảm giá cao hơn
                                if($bestRangeIndex == -1 || $currentDiscount > $bestDiscount){
                                    $bestDiscount = $currentDiscount;
                                    $bestPromotion = $promotion;
                                    $bestRangeIndex = $i;
                                }
                            } else {
                                // Nếu chưa có khoảng nào trong range, mới xét khoảng ngoài range
                                if($bestRangeIndex == -1 && $currentDiscount > $bestDiscount){
                                    $bestDiscount = $currentDiscount;
                                    $bestPromotion = $promotion;
                                }
                            }
                        }
                    }
                    
                    // Áp dụng khuyến mại tốt nhất tìm được
                    if($bestDiscount > 0){
                        // Chỉ cập nhật selectedPromotion nếu bestDiscount lớn hơn maxDiscount hiện tại
                        if($bestDiscount > $maxDiscount){
                            $maxDiscount = $bestDiscount;
                            $selectedPromotion = $bestPromotion;
                        }
                    }
                }
            }
        }
        return [
            'discount' => $maxDiscount,
            'selectedPromotion' => $selectedPromotion
        ];
    }

    public function getAppliedProductPromotions(){
        $carts = Cart::instance('shopping')->content();
        $appliedPromotions = [];
        $totalDiscount = 0;
        
        // Gọi reCaculateCart() để lấy danh sách promotion IDs THỰC SỰ được áp dụng
        $cartInfo = $this->reCaculateCart();
        $appliedPromotionIds = $cartInfo['appliedPromotionIds'] ?? [];
        
        // Tính tổng số tiền đã giảm
        foreach($carts as $cart){
            $originalPrice = $cart->priceOriginal ?? $cart->price;
            $discountedPrice = $cart->price;
            $quantity = $cart->qty;
            
            // Tính tiền giảm = (giá gốc - giá sau giảm) × số lượng
            $discount = ($originalPrice - $discountedPrice) * $quantity;
            if($discount > 0){
                $totalDiscount += $discount;
            }
        }
        
        // Lấy thông tin đầy đủ của các promotions đã được áp dụng
        if(count($appliedPromotionIds) > 0){
            $promotions = Promotion::whereIn('id', $appliedPromotionIds)->get();
            
            foreach($promotions as $promotion){
                $appliedPromotions[$promotion->id] = $promotion;
            }
        }
        
        return [
            'promotions' => array_values($appliedPromotions),
            'discount' => $totalDiscount
        ];
    }
   
}
