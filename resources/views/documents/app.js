const home = 'http://customer.temporaryview.com/document';
// defined home is rest api base url and need to change when website move to other address.

const pv = new Vue({ el: '#app',
  data: {
      postTitle: '',
      postContent: '',
      isLoading: true,
      nextTitle: '',
      nextUrl: '',
      hasNextUrl: false
  },
  created() {
      let loadUrl = home + '/wp-json/wp/v2/posts/1';
      if(localStorage.getItem("lastUrl") != null){
        loadUrl = localStorage.getItem("lastUrl");
        let postId = loadUrl.replace(home+'/','');
        loadUrl = postId.replace('/','');
        loadUrl = home + '/wp-json/wp/v2/posts/' + loadUrl;
      }   
      axios.get(loadUrl).then((Response)=>{
                        this.postTitle = Response.data.title.rendered;
                        this.postContent = Response.data.content.rendered;
                        pv.isLoading = false;  
      });
  },
  methods: {
    nextLinkHandler(){
              let navs = document.querySelectorAll('.nav');
              for(j = 0 ; j < navs.length ; j++){
                  if(navs[j].getAttribute('url') == this.nextUrl){
                    navs[j].click();
                  }
              }


    }
  }                  
});

Vue.component('post',
     {
         data: 
          function(){
                    return {
                        menus:[],
                    }
          },

          template: `
                 <div class="permenu">
                     <div  v-for="(menu, index) in menus" :key="menu.id">
                            <div :class="['nav menu', (index === 0 ? 'active' : '')]" @click="menuClickHandler" :url="menu.url">{{menu.title}}</div> 
                            <div class="nav subMenu" v-for="child in menu.children" :key="child.id" :url="child.url"
                                @click="menuClickHandler" > 
                                {{child.title}}
                            </div>
                     </div>
                 </div>
          `,
          created() {
                     axios.get(home + '/wp-json/wp-api-menus/v2/menus/2').then((Response)=>{
                        this.menus = Response.data.items;
                     }).then(()=>{
                          if(localStorage.getItem("lastUrl") != null){
                              let actives = document.querySelectorAll('.active');
                              for(let i = 0 ; i < actives.length ; i++){
                                actives[i].classList.remove('active');
                              }
                              let navs = document.querySelectorAll('.nav');
                              for( let i = 0 ; i < navs.length ; i++ ){
                                if(navs[i].getAttribute('url') == localStorage.getItem("lastUrl")){
                                   navs[i].classList.add('active');
                                   if(i+1 < navs.length){
                                       pv.hasNextUrl = true;
                                       pv.nextTitle = navs[i+1].innerText;
                                       pv.nextUrl = navs[i+1].getAttribute('url');   
                                   }
                                }                             
                              }
                            
                          } 
                       }
                     );
                     
          },
          methods:{
                    menuClickHandler(e){
                          
                          let actives = document.querySelectorAll('.sidebar .active');
                          for(let i = 0 ; i < actives.length ; i++){
                            actives[i].classList.remove('active');
                          }
                          e.target.classList.add('active');
                          let urlCliked = e.target.getAttribute('url');
                          let postId = urlCliked.replace(home+'/','');
                          postId = postId.replace('/','');
                          if(postId != '#' && postId != ''){
                             pv.isLoading = true;
                             axios.get(home + '/wp-json/wp/v2/posts/' + postId).then((Response)=>{
                                   pv.postTitle = Response.data.title.rendered;
                                   pv.postContent = Response.data.content.rendered;
                             pv.isLoading = false;
                             localStorage.setItem("lastUrl", urlCliked);  
                             document.querySelector('#to_top_scrollup').click(); 
                             findNext(e);  
                             });
                          }
           
                    }
            
          }
                
            
 });



const findNext = (e) => {
   let navs = document.querySelectorAll('.nav');
   for( let i = 0 ; i < navs.length ; i++ ){
      if(navs[i].getAttribute('url') == e.target.getAttribute('url')){
        
         if(i+1 < navs.length){
             pv.hasNextUrl = true;
             pv.nextTitle = navs[i+1].innerText;
             pv.nextUrl = navs[i+1].getAttribute('url');   
         }else{
             pv.hasNextUrl = false;
         }

      }                             
    }
  
}