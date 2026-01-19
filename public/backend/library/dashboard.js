(function($) {
	"use strict";
	var HT = {}; 


    HT.createChart = (label, data) => {

        let canvas = document.getElementById('barChart')
        let ctx = canvas.getContext('2d')

        if(window.myBarChart){
            window.myBarChart.destroy();
        }

        let chartData = {
            labels: label,
            datasets: [
                {
                    label: "Doanh thu",
                    backgroundColor: 'rgba(26,179,148,0.5)',
                    borderColor: "rgba(26,179,148,0.7)",
                    pointBackgroundColor: "rgba(26,179,148,1)",
                    pointBorderColor: "#fff",
                    data: data
                }
            ]
        }

        let chartOption = {
            tooltips: {
              callbacks: {
                    label: function(tooltipItem, data) {
                        var value = tooltipItem.yLabel;
                        value = value.toString();
                        value = value.split(/(?=(?:...)*$)/);
                        value = value.join('.');
                        return value;
                    }
              } 
            }, 
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero:true,
                        userCallback: function(value, index, values) {
                            // Convert the number to a string and splite the string every 3 charaters from the end
                            value = value.toString();
                            value = value.split(/(?=(?:...)*$)/);
                            value = value.join('.');
                            return value;
                        }
                    }
                }],
                xAxes: [{
                    ticks: {
                    }
                }]
            }
        }

        window.myBarChart = new Chart(ctx, {type: 'bar', data: chartData, options:chartOption});

    }

    HT.changeChart = () => {
        $(document).on('click', '.chartButton', function(e){
            e.preventDefault()
            let button = $(this)
            let chartType = button.attr('data-chart')
            $('.chartButton').removeClass('active')
            button.addClass('active')
            
            // Hiển thị/ẩn dropdown theo loại biểu đồ
            if(chartType == '1'){
                // Biểu đồ năm: chỉ hiện year
                $('#monthSelect').hide()
                $('#yearSelect').show()
                HT.updateChartTitle()
            } else if(chartType == '30'){
                // Theo tháng: hiện cả year và month
                $('#monthSelect').show()
                $('#yearSelect').show()
                HT.updateChartTitle()
            } else {
                // 7 ngày: ẩn cả hai
                $('#monthSelect').hide()
                $('#yearSelect').hide()
                $('#chartTitle').text('7 ngày gần nhất')
            }
            
            HT.callChart(chartType)
        })
    }

    HT.changeYear = () => {
        $(document).on('change', '#yearSelect', function(e){
            e.preventDefault()
            HT.updateChartTitle()
            
            // Gọi lại biểu đồ
            let activeButton = $('.chartButton.active')
            let chartType = activeButton.attr('data-chart')
            if(chartType == '1' || chartType == '30'){
                HT.callChart(chartType)
            }
        })
    }

    HT.changeMonth = () => {
        $(document).on('change', '#monthSelect', function(e){
            e.preventDefault()
            HT.updateChartTitle()
            
            // Gọi lại biểu đồ
            let activeButton = $('.chartButton.active')
            let chartType = activeButton.attr('data-chart')
            if(chartType == '30'){
                HT.callChart(chartType)
            }
        })
    }

    HT.updateChartTitle = () => {
        let activeButton = $('.chartButton.active')
        let chartType = activeButton.attr('data-chart')
        let selectedYear = $('#yearSelect').val()
        let selectedMonth = $('#monthSelect').val()
        
        if(chartType == '1'){
            $('#chartTitle').text('Năm ' + selectedYear)
        } else if(chartType == '30'){
            $('#chartTitle').text('Tháng ' + selectedMonth + '/' + selectedYear)
        }
    }

    HT.callChart = (chartType) => {
        let selectedYear = $('#yearSelect').val() || new Date().getFullYear()
        let selectedMonth = $('#monthSelect').val() || new Date().getMonth() + 1
        
        $.ajax({
            type        : 'GET',
            url         :  'ajax/order/chart',
            data		: {
                chartType : chartType,
                year: selectedYear,
                month: selectedMonth
            },
            dataType    : 'json',
            success: function(response){

                HT.createChart(response.label, response.data)
            }
        });
    }
   
   
	$(document).ready(function(){
        
        HT.createChart(label, data)

        HT.changeChart();

        HT.changeYear();

        HT.changeMonth();


	});

    

})(jQuery);
