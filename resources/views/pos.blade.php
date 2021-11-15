@php

@endphp

  <!DOCTYPE html>
  <html lang="en">
    <head>
      <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
      <meta charset="utf-8" />
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

      <link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Battambang:wght@400;700&display=swap" rel="stylesheet">
    <style>
        
        #invoice-POS {
           padding:1mm; 
            margin: 0 auto;
            width: 100%;
            background: #FFF;
            font-family: 'Battambang', 'arial';
            font-size: 30px;
           height: auto !important;
          }
      
      .flex {
/*           display: flex;
          justify-content: space-between;
          align-items: center; */
      }
      .flex::after {
  content: "";
  clear: both;
  display: table;
}
      
      .center {
          text-align: center;
      }
      
      .companyname {
            margin: 10px 0px;
            font-weight: bold;
          font-size: 18px;
      }
      
      .label {
            margin: 7px 0px;
            font-size: 16px;
      }
      
      .inv-line {
            margin: 16px 0px;
            border: 1px dashed #000;
        }
      
      .items {
            margin: 40px 0px;
          font-size: 15px;
       }
      
       .iname {
            float: left;
        }
      .total{float:right;}
      .comAddress {
          font-size: 14px;
          text-align: center;
          margin: 15px 0px;
      }
  
      .qty {
          font-size: 16px;
      }
      
      .total {
          padding-right: 10px;
      }
      
      
      /*::selection {background: #f31544; color: #FFF;}
      ::moz-selection {background: #f31544; color: #FFF;}*/

      /* img {
        -webkit-filter: grayscale(100%); /* Safari 6.0 - 9.0 */
        filter: grayscale(100%);
      } */

      h1{
        font-size: 9px;
        color: #222;
      }
      h2{font-size: 8.5px; color: black;}
      h3{
        font-size: 8px;
        font-weight: 300;
        line-height: 5px;
      }
      p{
        font-size: 10px;
        color: #000;
        line-height: 17px;
      }

      #top, #mid,#bot{ /* Targets all id with 'col-' */
        border-bottom: 1px solid #EEE;
      }

      #top{min-height: 50px;}
      #mid{min-height: 80px;} 
      #bot{ min-height: 50px;}

      #top .logo{
        /*float: left;*/
          height: 50px;
          width: 100%;
      }
      .clientlogo{
        float: left;
          height: 60px;
          width: 60px;
          background: url(http://michaeltruong.ca/images/client.jpg) no-repeat;
          background-size: 60px 60px;
        border-radius: 50px;
      }
      .info{
        display: block;
        width: 100%;
        /*float:left;*/
        margin-left: 0;
      }
      .title{
        float: right;
      }
      .title p{text-align: right;} 
      table{
        width: 100%;
        border-collapse: collapse;
      }
      td{
        /*/padding: 5px 0 5px 15px;
        //border: 1px solid #EEE*/
      }
      .tabletitle{
        /*/padding: 5px;*/
        font-size: 9.5px;
        background: #EEE;
      }
      .service{border-bottom: 1px solid #EEE;}
      .item{width: 24mm;}
      .itemtext{font-size: 10px; line-height: 10px}
      .trinfo h2{padding: 0; margin: 0;color: #000; line-height: 17px}

      #legalcopy{
        margin-top: 5mm;
      }



    </style>
    </head>

    <body class="no-skin">
      <div id="invoice-POS">
    
    <div class="center">
      <img src="{{ URL::asset('/resources/filelibrary/5520201588687700_sws_logo_update.png') }}" width="50%"/>
      <div class="companyname">
        Coffee Lovers
      </div>
    </div>
    <div class="label">
      Date: {{date("d-m-Y")}}
    </div>
    <div class="label">
      Reciept: INV-000013
    </div>
    <div class="label">
      Cashier: n/a
    </div>
    
    <div class="inv-line"></div>
    <div class="items">
            <div class="desc">
              <div class="flex">
                  <div class="iname">Coca Cola Can</div>
                  <div class="total">
                    $1.50 
                  </div>
              </div>
               
               <!-- End items name -->
              
               <div class="isize">
                    -
               </div>
              <!-- isize -->
              
              <div class="qty">
                $1.50 X 1PCS
              </div>
              <!--  End Qty -->
            </div>
        </div>
    <div class="items">
            <div class="desc">
              <div class="flex">
                  <div class="iname">Lacoste Shirt Jersey</div>
                  <div class="total">
                    $116.00 
                  </div>
              </div>
               
               <!-- End items name -->
              
               <div class="isize">
                    S - Gold
               </div>
              <!-- isize -->
              
              <div class="qty">
                $116.00 X 1PCS
              </div>
              <!--  End Qty -->
            </div>
        </div>
    <div class="inv-line"></div>
        
    <div class="label">
      Sub total: $117.50
    </div>
    
    <div class="label">
      Discount: $0.00
    </div>
    

    
    <div class="label" style="margin: 20px 0px; font-size: 18px;"> 
      <b>
      Total: $117.50 / 481,800.00៛
      </b> 
    </div>
    
    <div class="label">
      Received: $0.00 / 0.00៛
    </div>
    <div class="label">
      Change: $0.00 / 0.00៛
    </div>
    
    <div class="inv-line"></div>
    
    <div class="comAddress">
      NO.3AEO, STREET 230, SANGKAT PHSARDOEUMKOR, KHAN TOUL KOK, PHNOM PENH, CAMBODIA.
    </div>
    
    
    <div class="comAddress">
       <strong>សូមអគុណ</strong>
          <br>
       {{$header_contact_info['phone']??''}}
          <br>
        AnAoffice/i-POS [www.anaoffice.com]
                     
    </div>
     
    
    
  </div>
  <!--End Invoice-->
    </body>
  </html>
