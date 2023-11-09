
// search filter for feed list
jQuery("#searchFilter").on('keyup', function(){
let val = jQuery(this).val();
   searchFeed(val);
   
});
jQuery(document).on('click' , '.paginator ul li',function(e){
   e.preventDefault();
   var url = jQuery(this).find('a').attr('href');
   console.log(url);
   const urlParams = new URLSearchParams(url);
   const search = jQuery("#searchFilter").val();
   const page = urlParams.get('page');
   console.log(search,page, urlParams);
    searchFeed(search , page);
});

function searchFeed(search='',page=1){
  jQuery.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
    }
});
jQuery.ajax({
    url: 'feeds',
    type: 'GET',
    data: {
        "search": search, 
        "is_ajax": 1, 
        "page" :page
        },
    success: function(data)
    {
        jQuery(".searchList").html(data.html);
        jQuery(".paginator").html(data.link);
     }
 })

}
