<template>
    <div>
        <div class="images-to-upload">
            <div v-for="(file, index) in attachments" style="float:left;padding-right:10px" class="text-center">
                <img :src="file.url" width="100" height="100" class="image-common" style="display:block;">
                <span @click="removeAttachment(index)" class="remove-attachment">x</span>
            </div>
        </div>
        <br>
        <button
            class="button button-default"
            @click="fileUpload">+ {{ i18n.add_files }}</button>
    </div>
</template>

<style>
    .remove-attachment {
        cursor: pointer;
        border: 1px solid #d54e21;
        padding: 0px 5px;
        color: #d54e21;
        border-radius: 15px;
    }
</style>

<script>
    export default {
        components: {

        },
        props: ['attachments', 'i18n'],
        methods: {
            removeAttachment: function(index) {
                this.$emit('remove', index);
            },

            fileUpload: function() {
                var file_frame,
                    vm = this;
                    self = jQuery(this);
                if ( file_frame ) {
                    file_frame.open();
                    return;
                }
              // Create the media frame.
                file_frame = wpmedia.frames.file_frame = wpmedia({
                    title: jQuery( this ).data( 'uploader_title' ),
                    button: {
                        text: jQuery( this ).data( 'uploader_button_text' )
                    },
                    multiple: false
                });
                file_frame.on( 'select', function() {
                    var attachment = file_frame.state().get('selection').first().toJSON();
                    console.log(attachment);
                    vm.$emit('attach', attachment);
                    // vm.attachments.push(attachment);
                    // vm.attachmentIDs.push(attachment.id);
                    // var wrap = self.closest('.dokan-banner');
                    // wrap.find('input.dokan-file-field').val(attachment.id);
                    // wrap.find('img.dokan-banner-img').attr('src', attachment.url);
                    // jQuery('.image-wrap', wrap).removeClass('dokan-hide');
                    // jQuery('.button-area').addClass('dokan-hide');
                });
                file_frame.open();
            },
        },
        created() {
            console.log(this.attachments);
        },
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>
