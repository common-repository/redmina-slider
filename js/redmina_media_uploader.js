/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

jQuery(document).ready(function($){
    
    var mediaUploader;
    var val1;
    $('.upfile').click(function(e){
         var mytextfield=$(this);  
        e.preventDefault();
        if(mediaUploader){
            
            mediaUploader.open();
            return;
        }
        mediaUploader=wp.media.frames.file_frame=wp.media({
                title:'Choose a picture',
                button:{
                    text:'Choose a picture'
                } ,
                multiple:false
            } );
            mediaUploader.on('select',function(){
                attachment=mediaUploader.state().get('selection').first().toJSON();
              
                var initialVal=$("#file1").val();
             
            
               var slide=attachment.url+','+'text over slide';
               
                if(initialVal==""){
                   $("#file1").val(slide);
                    
                }
                else{
                
             $("#file1").val(initialVal+";"+slide);
             
           }
           
          alert("Please update to see the changes, push the top right update/publish button!");
            });
         mediaUploader.open();
    });
    $(".delete_img").click(function(){
        if($(this).is(':checked'))
        {
          
           
        var answer=confirm("Do you delete this picture ?");
      
        if(answer==true){
            
            var pictures=$("#file1").val().split(';');
            
           var dt=$(this).attr("data-bind");
           
                pictures.splice(dt,1); 
         $("#file1").val(pictures.join(";"));
         
                alert("Done , please update to see the changes push the top right update/publish button!");
        }
       
        }
    });
    $(".save_text_over_slide").click(function(){
        
        if($(this).is(':checked')){
            
            var dt=$(this).attr('data-bind');
            var id="text-over-slide"+dt;
             var newtext=$('#'+id).val();//the user has this in the textfield
          
            
            
          var pictures=$("#file1").val().split(";");
            
             for(i=0;i<pictures.length;i++){
                
                 if(i==dt){
                     var oldtext=pictures[dt].split(",");//this is in the hidden file
                       oldtext[1]=newtext;
                       pictures[dt]=oldtext;
                 }
                
               }
         
            $("#file1").val(pictures.join(";"));
        
        }
        
        
    });
});
       