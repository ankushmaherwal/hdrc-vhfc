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
};
