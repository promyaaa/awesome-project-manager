<template>
    <div>
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <router-link :to="'/projects/' + $route.params.projectid" tag="span" class="link-style">
                        <a>{{project.project_title}}</a> >
                    </router-link>
                    <router-link :to="'/projects/' + $route.params.projectid + '/messages/'" tag="span" class="link-style">
                        <a>Messages Board</a> > New Message
                    </router-link>
                    <!-- <router-link :to="'/projects/' + $route.params.projectid + '/messages'" class="link-style" tag="span">
                        <a>Message Board</a>
                    </router-link> -->
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="list-title">
                        <!-- <a class="page-title-action" @click.prevent="toggleMessageForm" v-if="!isShowMessageForm">+ Add</a> -->
                    </div>
                    <div>
                        <!-- <vue-editor v-model="content" :editorToolbar="customToolbar"></vue-editor> -->
                        <div class="add_form_style">
                            <div>
                                <input type="text" 
                                       v-model="messageTitle"
                                       class="form-control"
                                       v-focus
                                       placeholder="add message title">
                            </div>
                            <!-- <div>
                                <textarea 
                                    name="message" 
                                    v-model="message" 
                                    class="form-control" 
                                    placeholder="message . . ." 
                                    @keyup.enter="createMessage" 
                                    @keyup.esc="toggleMessageForm"
                                    rows="3"></textarea>
                            </div> -->
                            <div>
                                <vue-editor v-model="message" :editorToolbar="customToolbar"></vue-editor>
                            </div>
                            <br>
                            <file-upload 
                                v-on:attach="updateAttachments" 
                                v-on:remove="removeAttachment" 
                                :attachments="attachments"></file-upload>
                            <br>    
                            <div class="action">
                                <button class="button button-primary" @click.prevent="createMessage">
                                    Add
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- <div class="row"></div> -->
        </div>
    </div>
</template>

<style>
    .ql-container {
        background: #ffffff;
    }
    .messages-section {
        background-color: #fff;
        padding: 15px;
    }
    .images-to-upload {
        display: block;
        overflow: hidden;
        margin-bottom: 10px;
    }
</style>

<script>
    import { VueEditor } from 'vue2-editor'
    import FileUpload from './FileUploadComponent.vue';
    export default {
        components: {
            VueEditor,
            FileUpload
        },
        directives: {
            focus: {
                inserted: function (el) {
                    el.focus()
                }
            }
        },

        data() {
            return {
                // messages: [],
                // loading: false,
                mindex: 0,
                isShowMessageForm: false,
                message: '',
                projectTitle: '',
                messageTitle: '',
                attachments: [],
                attachmentIDs: [],
                project: {},
                // content: '',
                customToolbar : [
                  ['bold', 'italic', 'underline', 'strike'],
                  ['blockquote', 'code-block'], // 'image' can be added
                  [{ 'list': 'ordered'}, { 'list': 'bullet' }], // { 'list': 'check' }
                  [{ 'indent': '-1'}, { 'indent': '+1' }],
                  // [{ 'header': [1, 2, 3, 4, 5, 6, false] }], //[1, 2, 3, 4, 5, 6, false]
                  // [{ 'color': [] }, { 'background': [] }],
                  // [{ 'font': [] }],
                  [{ 'align': [] }],
                  // ['clean']
                ]

            }
        },

        methods: {
            updateAttachments: function(attachment) {
                var vm = this;

                vm.attachments.push(attachment);
                vm.attachmentIDs.push(attachment.id);
            },

            removeAttachment: function(index) {
                this.attachments.splice(index, 1);
                this.attachmentIDs.splice(index, 1);
            },
            
            toggleMessageForm: function() {
                var vm = this;

                vm.messageTitle = '';
                vm.message = '';
                vm.isShowMessageForm = !vm.isShowMessageForm;
            },


            // imageUpload: function() {
            //     var file_frame,
            //         vm = this;
            //         self = jQuery(this);
            //     if ( file_frame ) {
            //         file_frame.open();
            //         return;
            //     }
            //   // Create the media frame.
            //     file_frame = wpmedia.frames.file_frame = wpmedia({
            //         title: jQuery( this ).data( 'uploader_title' ),
            //         button: {
            //             text: jQuery( this ).data( 'uploader_button_text' )
            //         },
            //         multiple: false
            //     });
            //     file_frame.on( 'select', function() {
            //         var attachment = file_frame.state().get('selection').first().toJSON();
            //         console.log(attachment);
            //         vm.attachments.push(attachment);
            //         vm.attachmentIDs.push(attachment.id);
            //         // var wrap = self.closest('.dokan-banner');
            //         // wrap.find('input.dokan-file-field').val(attachment.id);
            //         // wrap.find('img.dokan-banner-img').attr('src', attachment.url);
            //         // jQuery('.image-wrap', wrap).removeClass('dokan-hide');
            //         // jQuery('.button-area').addClass('dokan-hide');
            //     });
            //     file_frame.open();
            // },

            createMessage: function() {
                var vm = this,
                message,
                messageID,
                projectID = vm.$route.params.projectid,
                data = {
                    action : 'fpm-insert-message',
                    nonce : fpm.nonce,
                    message_title: vm.messageTitle,
                    project_title: vm.project.project_title,
                    message: vm.message,
                    project_id: projectID,
                    attachments: vm.attachmentIDs
                };

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                        console.log(vm.$route);
                    if ( resp.success ) {
                        messageID = resp.data.messageInfo.ID;
                        projectID = data.project_id;
                        vm.$router.push({ 
                            path: `/projects/${projectID}/messages/${messageID}` 
                        });
                        // resp.data.messageInfo.message = vm.message;
                        // vm.messages.unshift(resp.data.messageInfo);
                        // vm.message = '';
                        // vm.messageTitle = '';
                    } else {
                        // vm.message = resp.data;
                    }
                });
            },

            fetchProjectInfo: function() {
                var vm = this;
                // vm.loading = true;
                
                var data = {
                    action: 'fpm-get-project',
                    project_id: vm.$route.params.projectid,
                    nonce: fpm.nonce,
                };

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    vm.loading = false;
                    console.log(resp);
                    if ( resp.success ) {
                        vm.project = resp.data[0];
                    }
                });
            },
        },

        created() {
            this.fetchProjectInfo();
        },

        mounted() {

        }
    }
</script>