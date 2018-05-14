<template>
    <div>
        <div class="images-to-upload">
            <div v-for="(file, index) in attachments" style="float:left;padding-right:10px" class="text-center">
                <files-type-display :file="file" type="small"></files-type-display>
                <span @click="removeAttachment(index)" class="remove-attachment">x</span>
            </div>
        </div>
        <br>
        <p class="howto">
            Note: png, jpeg, gif, plaintext, html, css, csv, js, pdf, tar, zip, gzip, rar, 7z, doc, pot, pps, ppt, docx, odt, odp files can be uploaded
        </p>
        <button
            class="button button-default"
            @click="fileUpload">+ {{ i18n.add_files }}</button>
    </div>
</template>

<style>
    .remove-attachment {
        margin-top: 5px;
        cursor: pointer;
        border: 1px solid #d54e21;
        padding: 0px 5px;
        color: #d54e21;
        border-radius: 15px;
    }
</style>

<script>
    import FilesTypeDisplay from './FilesTypeDisplay.vue'
    export default {
        components: {
            FilesTypeDisplay
        },
        props: ['attachments', 'i18n'],
        methods: {
            removeAttachment: function(index) {
                this.$emit('remove', index);
            },

            fileUpload: function() {
                var file_frame,
                    vm = this,
                    mime_array,
                    attachment,
                    isAllowedType;

                mime_array = [
                    'image/jpeg',
                    'image/gif',
                    'image/png',
                    'text/plain',
                    'text/csv',
                    'text/css',
                    'text/html',
                    'application/javascript',
                    'application/pdf',
                    'application/x-tar',
                    'application/zip',
                    'application/x-gzip',
                    'application/rar',
                    'application/x-7z-compressed',
                    'application/msword',
                    'application/vnd.ms-powerpoint',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.oasis.opendocument.text',
                    'application/vnd.oasis.opendocument.presentation',
                ]

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
                    attachment = file_frame.state().get('selection').first().toJSON();
                    // console.log(attachment);
                    isAllowedType = mime_array.includes(attachment.mime);

                    if(isAllowedType) {
                        vm.$emit('attach', attachment);    
                    } else {
                        // console.log('not allowed');
                    }
                });
                file_frame.open();
            },
        },
        created() {
            
        },
        mounted() {
            
        }
    }
</script>
