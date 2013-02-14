$(document).ready(function(){
	
	// REGISTER CASE
	$(".frm_register_case").live("submit", function(e){
		e.preventDefault();

		var thisForm = $(this);
		var submitBnt = $(thisForm).find("input[type='submit']");
		var type = $(thisForm).find("input[name='type']").val();
		var requestUrl = $(thisForm).attr("action");
    
    var location_code = $("#choose_building_on_property  option:selected").val();
    
    $(thisForm).find("input[name=location_code]").val(location_code);
    
		$.ajax({
			  type: 'POST',
			  url: requestUrl + "&" + $(thisForm).serialize(),
			  success: function(data) {
				  if(data){
	    			  var jsonObj = jQuery.parseJSON(data);
		    		
	    			  if(jsonObj.status == "saved"){
		    			  var submitBnt = $(thisForm).find("input[type='submit']");
		    			  $(submitBnt).val("Lagret");	
		    			  
		    			  clear_form( thisForm );
			      				  
		    			  // Changes text on save button back to original
		    			  window.setTimeout(function() {
		    				  if( type == "control_item_type_2")
		    					  $(submitBnt).val('Lagre måling');
		    				  else
		    					  $(submitBnt).val('Lagre sak');
		    				  
							$(submitBnt).addClass("not_active");
		    			  }, 1000);

		    			  $(thisForm).delay(1500).slideUp(500, function(){
		    				  $(thisForm).parents("ul.expand_list").find("h4 img").attr("src", "controller/images/arrow_right.png");  
		    			  });
					  }
				  }
				}
		});
	});

	// UPDATE CASE
	$(".frm_update_case").live("submit", function(e){
		e.preventDefault();

		var thisForm = $(this);
		var clickRow = $(this).closest("li");
		var checkItemRow = $(this).closest("li.check_item_case");
		var requestUrl = $(thisForm).attr("action");
				
		$.ajax({
			  type: 'POST',
			  url: requestUrl + "&" + $(thisForm).serialize(),
			  success: function(data) {
				  if(data){
	    			  var jsonObj = jQuery.parseJSON(data);
		 
	    			  if(jsonObj.status == "saved"){
	    				var type = $(thisForm).find("input[name=control_item_type]").val();
	    				
		    			if(type == "control_item_type_1"){
		    				var case_status = $(thisForm).find("select[name='case_status'] option:selected").text();
	    					
	    					$(clickRow).find(".case_info .case_status").empty().text( case_status );
	    				}
		    			else if(type == "control_item_type_2"){
	    					var case_status = $(thisForm).find("select[name='case_status'] option:selected").text();
	    					
	    					$(clickRow).find(".case_info .case_status").empty().text( case_status );
	    					
	    					var measurement_text = $(thisForm).find("input[name='measurement']").val();
		    				$(clickRow).find(".case_info .measurement").text(measurement_text);
	    				}
	    				else if(type == "control_item_type_3"){
	    					var case_status = $(thisForm).find("select[name='case_status'] option:selected").text();
	    					
	    					$(clickRow).find(".case_info .case_status").empty().text( case_status );
	    					
	    					var measurement_text = $(thisForm).find("select[name='measurement'] option:selected").val();
		    				$(clickRow).find(".case_info .measurement").text(measurement_text);
	    				}
	    				else if(type == "control_item_type_4"){
	    					var case_status = $(thisForm).find("select[name='case_status'] option:selected").text();
	    					
	    					$(clickRow).find(".case_info .case_status").empty().text( case_status );
	    					
	    					var measurement_text = $(thisForm).find("input:radio[name='measurement']:checked").val();
		    				$(clickRow).find(".case_info .measurement").text(measurement_text);
	    				}
		    			
		    			// Text from forms textarea
	    				var desc_text = $(thisForm).find("textarea").val();
	    				// Puts new text into description tag in case_info	    				   				
	    				$(clickRow).find(".case_info .case_descr").text(desc_text);
	    					    				
	    				$(clickRow).find(".case_info").show();
	    				$(clickRow).find(".frm_update_case").hide();
					  }
				  }
			  }
		});
	});
	
	$("a.quick_edit_case").live("click", function(e){
		var clickElem = $(this);
		var clickRow = $(this).closest("li");
									
		$(clickRow).find(".case_info").hide();
		$(clickRow).find(".frm_update_case").show();
		
		return false;	
	});
	
	$(".frm_update_case .cancel").live("click", function(e){
		var clickElem = $(this);
		var clickRow = $(this).closest("li");
				
		
		$(clickRow).find(".case_info").show();
		$(clickRow).find(".frm_update_case").hide();
		
		return false;	
	});
	
	// DELETE CASE
	$(".delete_case").live("click", function(){
		var clickElem = $(this);
		var clickRow = $(this).closest("li");
		var clickItem = $(this).closest("ul");
		var checkItemRow = $(this).parents("li.check_item_case");
		
		var url = $(clickElem).attr("href");
	
		// Sending request for deleting a control item list
		$.ajax({
			type: 'POST',
			url: url,
			success: function(data) {
				var obj = jQuery.parseJSON(data);
		    		
   			  	if(obj.status == "deleted"){
	   			  	if( $(clickItem).children("li").length > 1){
	   			  		$(clickRow).fadeOut(300, function(){
	   			  			$(clickRow).remove();
	   			  		});
	   			  		
		   			  	var next_row = $(clickRow).next();
						
						// Updating order numbers for rows below deleted row  
						while( $(next_row).length > 0){
							update_order_nr_for_row(next_row, "-");
							next_row = $(next_row).next();
						}
	   			  	}else{
		   			  	$(checkItemRow).fadeOut(300, function(){
	   			  			$(checkItemRow).remove();
	   			  		});
	   			  	}
   			  	}
			}
		});

		return false;
	});
	
	// CLOSE CASE
	$(".close_case").live("click", function(){
		var clickElem = $(this);
		var clickRow = $(this).closest("li");
		var clickItem = $(this).closest("ul");
		var checkItemRow = $(this).parents("li.check_item_case");
		
		var url = $(clickElem).attr("href");
	
		// Sending request for deleting a control item list
		$.ajax({
			type: 'POST',
			url: url,
			success: function(data) {
				var obj = jQuery.parseJSON(data);
		    		
   			  	if(obj.status == "true"){
	   			  	if( $(clickItem).children("li").length > 1){
	   			  		$(clickRow).fadeOut(300, function(){
	   			  			$(clickRow).remove();
	   			  		});
	   			  		
		   			  	var next_row = $(clickRow).next();
						
						// Updating order numbers for rows below deleted row  
						while( $(next_row).length > 0){
							update_order_nr_for_row(next_row, "-");
							next_row = $(next_row).next();
						}
	   			  	}else{
		   			  	$(checkItemRow).fadeOut(300, function(){
	   			  			$(checkItemRow).remove();
	   			  		});
	   			  	}
   			  	}
			}
		});

		return false;
	});
	
	// OPEN CASE
	$(".open_case").live("click", function(){
		var clickElem = $(this);
		var clickRow = $(this).closest("li");
		var clickItem = $(this).closest("ul");
		var checkItemRow = $(this).parents("li.check_item_case");
		
		var url = $(clickElem).attr("href");
	
		// Sending request for deleting a control item list
		$.ajax({
			type: 'POST',
			url: url,
			success: function(data) {
				var obj = jQuery.parseJSON(data);
		    		
   			  	if(obj.status == "true"){
	   			  	if( $(clickItem).children("li").length > 1){
	   			  		$(clickRow).fadeOut(300, function(){
	   			  			$(clickRow).remove();
	   			  		});
	   			  		
		   			  	var next_row = $(clickRow).next();
						
						// Updating order numbers for rows below deleted row  
						while( $(next_row).length > 0){
							update_order_nr_for_row(next_row, "-");
							next_row = $(next_row).next();
						}
	   			  	}else{
		   			  	$(checkItemRow).fadeOut(300, function(){
	   			  			$(checkItemRow).remove();
	   			  		});
	   			  	}
   			  	}
			}
		});

		return false;
	});
	
});
