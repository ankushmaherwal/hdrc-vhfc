jQuery(document).ready(function(){
	document.formvalidator.setHandler('contactnumber', function (value) {

		if (isNaN(value))
		{
			return false;
		}

		if (value.length === 10)
		{
			return true;
		}
		else
		{
			return false;
		}
	});
});

var tms = {
	common: {
		renderMessages: function (returnedData){
			if (returnedData.messages !== null)
			{
				if (returnedData.messages.error !== null)
				{
					jQuery.each(returnedData.messages.error, function(index, value) {
						Joomla.renderMessages({'error':[value]});
					});

					jQuery("html, body").animate({ scrollTop: 0 }, "slow");
				}
			}

			if (returnedData.messages !== null)
			{
				if (returnedData.messages.warning !== null)
				{
					jQuery.each(returnedData.messages.warning, function(index, value) {
						Joomla.renderMessages({'warning':[value]});
					});

					jQuery("html, body").animate({ scrollTop: 0 }, "slow");
				}
			}

			if (returnedData.message !== null && returnedData.message != '')
			{
				Joomla.renderMessages({'info':[returnedData.message]});

				jQuery("html, body").animate({ scrollTop: 0 }, "slow");
			}
		}
	},
	manageFreight: {
		addFreightEntry: function (obj, destination){
			var clone = jQuery(obj).parent().parent().parent().clone(false);
			var rowCount = jQuery(obj).closest("tbody").find("tr").length - 1;

			/* code to change name and ids of elements in new clone - start*/
			clone.find(".freight-item-box-weight").attr("name", "freightdata["+destination+"]["+rowCount+"][box_weight]");
			clone.find(".freight-item-box-weight").attr("value", "");
			clone.find(".freight-item-freight").attr("name", "freightdata["+destination+"]["+rowCount+"][freight]");
			clone.find(".freight-item-freight").attr("value", "");
			clone.find(".freight-item-inam").attr("name", "freightdata["+destination+"]["+rowCount+"][inam]");
			clone.find(".freight-item-inam").attr("value", "");

			/* code to change name and ids of elements in new clone - end*/
			jQuery(obj).parent().parent().parent().parent().append(clone);
		},

		autoFillFreight: function (obj){
			let destination = jQuery("#jform_destination").val();

			if (destination !== '' || destination !== undefined)
			{
				jQuery.ajax({
					url:Joomla.getOptions('system.paths').base +'/index.php?option=com_tms&task=getFreight&format=raw&tmpl=component&destination='+destination,
					type: 'post',
					dataType : 'json',
					success : function(response)
					{
						var freightData = JSON.parse(response.box_weight);

						let weightElement = jQuery(obj).attr('id');
						let freightElement = weightElement.replace("weight", "freight");
						let inamElement = weightElement.replace("weight", "inam");
						let flag = 0;

						if (freightData)
						{
							for (i = 0; i < freightData.length; ++i)
							{
								if (jQuery(obj).val() == freightData[i].box_weight)
								{
									jQuery("#"+freightElement).val(freightData[i].freight);
									jQuery("#"+inamElement).val(freightData[i].inam);
									flag = 1;
									break;
								}
							}
						}

						if (flag == 0)
						{
							jQuery("#"+freightElement).val('');
							jQuery("#"+inamElement).val('');
						}
					}
				});
			}
		},

		removeFreightEntry: function (obj){
			let rowCount = jQuery(obj).closest("tbody").find(".freight-item-row").length;

			if (rowCount > 1)
			{
				jQuery(obj).parent().parent().parent().remove();
			}
			else
			{
				return false;
			}
		}
	},
	manageAccount: {
		openAccountForm: function (){
			SqueezeBox.open(Joomla.getOptions('system.paths').base+'/index.php?option=com_tms&view=account&layout=edit&popup=1&tmpl=component' ,{handler: 'iframe', size: {x: window.innerWidth-250, y: window.innerHeight-150}});
		},
		saveAccount: function (){
			if (!document.formvalidator.isValid('#adminForm'))
			{
				return false;
			}

			let callurl = Joomla.getOptions('system.paths').base+"/index.php?option=com_tms&task=account.popupSave&tmpl=component&format=json";
			let formData = jQuery('#adminForm').serialize();

			/* Disable the save button onclick */
			jQuery('button').attr('disabled', true);

			jQuery.ajax({
				url: callurl,
				data: formData,
				type: "POST",
				cache: false,
				success: function(returnedData)
				{
					/* To render error and warnings */
					tms.common.renderMessages(returnedData);

					if (returnedData.data !== null)
					{
						if (returnedData.data.id !== '' && returnedData.data.title !=='')
						{
							jQuery(".tms-sender-party", parent.document).each(function(i) {
								jQuery(this).append(jQuery("<option></option>").attr("value", returnedData.data.id).text(returnedData.data.title));
								window.parent.tmsUpdateChzn(this.id);
							});
						}

						/* Set the value of id field */
						jQuery("#jform_id").val(returnedData.data.id);

						/* Enable the save button once the save operation is completed*/
						jQuery('button').attr('disabled', false);

						/* Close the squeezebox */
						window.parent.SqueezeBox.close();
					}
				}
			});
		}
	},
	manageVehicle: {
		openVehicleForm: function (){
			SqueezeBox.open(Joomla.getOptions('system.paths').base+'/index.php?option=com_tms&view=vehicle&layout=edit&popup=1&tmpl=component' ,{handler: 'iframe', size: {x: window.innerWidth-250, y: window.innerHeight-150}});
		},
		saveVehicle: function (){
			if (!document.formvalidator.isValid('#adminForm'))
			{
				return false;
			}

			let callurl = Joomla.getOptions('system.paths').base+"/index.php?option=com_tms&task=vehicle.popupSave&tmpl=component&format=json";
			let formData = jQuery('#adminForm').serialize();

			/* Disable the save button onclick */
			jQuery('button').attr('disabled', true);

			jQuery.ajax({
				url: callurl,
				data: formData,
				type: "POST",
				cache: false,
				success: function(returnedData)
				{
					/* To render error and warnings */
					tms.common.renderMessages(returnedData);

					if (returnedData.data !== null)
					{
						if (returnedData.data.id !== '' && returnedData.data.title !=='')
						{
							jQuery(".tms-vehicle", parent.document).each(function(i) {
								jQuery(this).append(jQuery("<option></option>").attr("value", returnedData.data.id).text(returnedData.data.title));
								window.parent.tmsUpdateChzn(this.id);
							});
						}

						/* Set the value of id field */
						jQuery("#jform_id").val(returnedData.data.id);

						/* Enable the save button once the save operation is completed*/
						jQuery('button').attr('disabled', false);

						/* Close the squeezebox */
						window.parent.SqueezeBox.close();
					}
				}
			});
		}
	}
};
