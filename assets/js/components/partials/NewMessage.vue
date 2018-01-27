<template>
    <div>
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <router-link :to="'/projects/' + $route.params.projectid " class="link-style inline-block" tag="h3">
                        <a>{{project.project_title}}</a>
                    </router-link>
                    <router-link :to="'/projects/' + $route.params.projectid + '/messages/'" class="link-style inline-block" tag="h4">
                        <a><i class="fa fa-long-arrow-right p-l-10 p-r-10" aria-hidden="true"></i>{{ i18n.message_label }}</a>
                    </router-link>
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
                                       :placeholder="i18n.message_title_placeholder">
                            </div>
                            <div>
                                <vue-editor v-model="message" :editorToolbar="customToolbar"></vue-editor>
                            </div>
                            <br>
                            <file-upload
                                :i18n="i18n"
                                v-on:attach="updateAttachments"
                                v-on:remove="removeAttachment"
                                :attachments="attachments"></file-upload>
                            <br>
                            <div class="action">
                                <button class="button button-primary" @click.prevent="createMessage">
                                    {{ i18n.post_new_msg_btn }}
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
    import store from '../../store';
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
                i18n:{},
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
            var vm = this;
            store.setLocalization( 'fpm-get-new-message-local-data' ).then( function( data ) {
                vm.i18n = data;
            });

            this.fetchProjectInfo();
        },

        mounted() {

        }
    }
</script>