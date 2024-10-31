/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

jQuery(document).ready(function($){
  
  
  $("input[name=redmina_slider_interval]").focusout(function(event){
  
  var tr_int=$("input[name=redmina_slider_interval]").val();
  var yes=  $.isNumeric(tr_int);
 
  if(tr_int<0){ yes=false; }
  if(yes!=true){ 
         $("#label_for_redmina_slider_interval").text("Only positive numbers ,please");
    
      }else{
          $("#label_for_redmina_slider_interval").text('');
      }
  
   });
   });

