<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\FrontendController;
use App\Services\CartService;
use App\Repositories\Interfaces\ProductRepositoryInterface as ProductRepository;
use Illuminate\Http\Request;
use Cart;
use App\Services\PromotionService;


class CartController extends FrontendController
{
    protected $cartService;
    protected $productRepository;
    protected $language;

    public function __construct(
        CartService $cartService,
        ProductRepository $productRepository,
    ){
        $this->cartService = $cartService;
        $this->productRepository = $productRepository;
        parent::__construct(); 
    }

    public function create(Request $request){
        $flag = $this->cartService->create($request, $this->language);

        $cart = Cart::instance('shopping')->content();
        
        return response()->json([
            'cart' => $cart, 
            'messages' => 'Thêm sản phẩm vào giỏ hàng thành công',
            'code' => ($flag) ? 10 : 11,
        ]); 
        
    }

    public function update(Request $request){
        $response = $this->cartService->update($request);
        
        // Lấy thông tin khuyến mại đang áp dụng
        $carts = \Cart::instance('shopping')->content();
        $originalTotal = 0;
        foreach($carts as $cart){
            $originalPrice = isset($cart->priceOriginal) ? $cart->priceOriginal : $cart->price;
            $originalTotal += $originalPrice * $cart->qty;
        }
        
        $productPromotions = $this->cartService->getAppliedProductPromotions();
        $cartPromotion = $this->cartService->cartPromotion($originalTotal);
        
        $promotionInfo = [
            'productDiscount' => $productPromotions['discount'],
            'productPromotions' => $productPromotions['promotions'] ?? [],
            'orderDiscount' => $cartPromotion['discount'],
            'orderPromotion' => $cartPromotion['selectedPromotion'] ?? null,
        ];
        
        return response()->json([
            'response' => $response,
            'promotion' => $promotionInfo,
            'messages' => 'Cập nhật số lượng thành công',
            'code' => (!$response) ? 11 : 10,
        ]); 
    }

    public function delete(Request $request){
        $response = $this->cartService->delete($request);
        
        // Lấy thông tin khuyến mại đang áp dụng
        $carts = \Cart::instance('shopping')->content();
        $originalTotal = 0;
        foreach($carts as $cart){
            $originalPrice = isset($cart->priceOriginal) ? $cart->priceOriginal : $cart->price;
            $originalTotal += $originalPrice * $cart->qty;
        }
        
        $productPromotions = $this->cartService->getAppliedProductPromotions();
        $cartPromotion = $this->cartService->cartPromotion($originalTotal);
        
        $promotionInfo = [
            'productDiscount' => $productPromotions['discount'],
            'productPromotions' => $productPromotions['promotions'] ?? [],
            'orderDiscount' => $cartPromotion['discount'],
            'orderPromotion' => $cartPromotion['selectedPromotion'] ?? null,
        ];
        
        return response()->json([
            'response' => $response,
            'promotion' => $promotionInfo,
            'messages' => 'Xóa sản phẩm khỏi giỏ hàng thành công',
            'code' => (!$response) ? 11 : 10,
        ]);  
    }

   
}
