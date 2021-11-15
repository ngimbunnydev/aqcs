<!doctype html>
<html >
  <head>
    <meta charset="utf-8">
    <title>AnA Office Document</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Battambang:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
    <link rel='stylesheet' id='wp-block-library-css'  href='http://customer.temporaryview.com/document/wp-includes/css/dist/block-library/style.min.css?ver=5.6' media='all' />
    <link href="{{ asset('resources/views/documents/app.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.12"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
  </head>
  <body>
    <div id="app">
      <div class="header-ipos fixed-top">
      <div class="container">
          <a >
              <div class="logo">
               <i class="fas fa-file-alt"></i> ឯកសារអំពីរបៀបប្រើ I-POS
                <div class="lds-spinner" v-show="isLoading"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
              </div>
          </a>
          <div class="header-right">
            <div class="search-box">
              <form>
                <input type="text" name="searchText" placeholder="ស្វែងរក ...">
                <i class="fas fa-search"></i>
              </form>
            </div>

            <div id="mobile-toggle" >
                <i class="fas fa-bars"></i>
            </div>
          </div>
      </div>
    </div>  
      <!-- End header ipos   -->
      <!-- container wrapper -->
      <div class="container wrapper">
          <div class="sidebar">
              <post></post>
          </div>
         <div class="main-content">
                  <div class="post-title"> @{{postTitle}} </div>
                  <div v-html="postContent"></div>
                  <div v-show="hasNextUrl" class="center">
                     <div @click="nextLinkHandler" class="next-post" :url="nextUrl">
                       @{{nextTitle}} <i class="fas fa-angle-double-right"></i>
                     </div> 
                  </div>
          </div>
      </div>
      <!-- End container wrapper -->
    </div>
    <!-- end app div  -->
  
    <script src="{{ asset('resources/views/documents/app.js') }}"></script>
  </body>
</html>
  