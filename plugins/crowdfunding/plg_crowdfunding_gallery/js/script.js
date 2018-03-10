jQuery(document).ready(function() {

    var fields = {
        object_id: jQuery("#js-cfgallery-project-id").val(),
        category_id: jQuery("#js-cfgallery-category-id").val(),
        task: 'entity.upload',
        format: 'raw',
        extension: 'com_crowdfunding'
    };

    // Set the token.
    var token = jQuery("#js-cfextras-form-token").serializeJSON();
    jQuery.extend(fields, token);

    // Add image
    jQuery('#js-cfgallery-fileupload').fileupload({
        dataType: 'text json',
        formData: fields,
        singleFileUploads: true,
        send: function () {
            jQuery("#js-cfgallery-ajax-loader").show();
        },
        fail: function () {
            jQuery("#js-cfgallery-ajax-loader").hide();
        },
        done: function (event, response) {

            if (!response.result.success) {
                PrismUIHelper.displayMessageFailure(response.result.title, response.result.text);
            } else {
                var element = jQuery("#js-cfgallery-row-template").clone(false);

                if (response.result.data.link_thumbnail) {
                    var imageLink = response.result.data.link_thumbnail;
                } else {
                    var imageLink = response.result.data.link_image;
                }

                jQuery(element).attr("id", "js-cfgallery-item" + response.result.data.id);
                jQuery(element).find("#js-cfgallery-rt-img").attr("src", imageLink).removeAttr('id');
                jQuery(element).find("#js-cfgallery-rt-title").text(response.result.data.title).removeAttr('id');
                jQuery(element).removeAttr("style");

                jQuery(element)
                    .find("#js-cfgallery-rt-remove")
                    .data("item-id", response.result.data.id)
                    .removeAttr('id')
                    .addClass("js-cfgallery-btn-remove");

                jQuery("#js-cfgallery-list").append(element);
            }

            // Hide ajax loader.
            jQuery("#js-cfgallery-ajax-loader").hide();
        }
    });

    jQuery("#js-cfgallery-list").on("click", ".js-cfgallery-btn-remove", function (event) {
        event.preventDefault();

        if (confirm(Joomla.JText._('PLG_CROWDFUNDING_GALLERY_DELETE_QUESTION'))) {

            var itemId = parseInt(jQuery(this).data("item-id"));

            if (itemId > 0) {
                var fields = {
                    entity_id: itemId,
                    object_id: jQuery("#js-cfgallery-project-id").val(),
                    category_id: jQuery("#js-cfgallery-category-id").val(),
                    task: 'entities.remove',
                    format: 'raw',
                    extension: 'com_crowdfunding'
                };

                // Set the token.
                var token = jQuery("#js-cfextras-form-token").serializeJSON();
                jQuery.extend(fields, token);

                jQuery.ajax({
                    url: "index.php?option=com_magicgallery",
                    type: "POST",
                    data: fields,
                    dataType: "text json",
                    beforeSend: function () {
                        jQuery("#js-cfgallery-ajax-loader").show();
                    }
                }).fail(function () {
                    jQuery("#js-cfgallery-ajax-loader").hide();
                }).done(function (response) {

                    if (response.success) {
                        jQuery("#js-cfgallery-item" + response.data.entity_id).remove();
                        PrismUIHelper.displayMessageSuccess(response.title, response.text);
                    } else {
                        PrismUIHelper.displayMessageFailure(response.title, response.text);
                    }

                    // Hide ajax loader.
                    jQuery("#js-cfgallery-ajax-loader").hide();

                });
            }
        }
    });

});