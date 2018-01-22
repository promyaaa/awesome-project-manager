<template>
    <div>
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <router-link :to="'/projects/' + $route.params.projectid" tag="span" class="link-style">
                        <a>{{messageObject.project_title}}</a> >
                    </router-link>
                    <router-link :to="'/projects/' + $route.params.projectid + '/messages'" class="link-style" tag="span">
                        <a>Message Board</a> >
                    </router-link>
                    <router-link :to="'/projects/' + $route.params.projectid + '/messages/' + $route.params.messageid" tag="span" class="link-style">
                        <a>{{messageObject.message_title}}</a> > Edit Message
                    </router-link>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div>
                        <div class="add_form_style">
                            <div>
                                <input type="text" 
                                       v-model="messageTitle"
                                       class="form-control"
                                       v-focus
                                       placeholder="add message title">
                            </div>
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
                                <button class="button button-primary" 
                                        @click.prevent="updateMessage"
                                        >update</button>
                                <!-- <button class="button button-default" @click="toggleMessageForm">Cancel</button> -->
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
    .messages-section {
        background-color: #fff;
        padding: 15px;
    }
    .images-to-upload {
        display: block;
        overflow: hidden;
        margin-bottom: 10px;
    }
    .message-edit-content {
        padding: 40px 60px;
        background: #ffffff;
        border-radius: 5px;
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
                messageObject: {},
                customToolbar : [
                  ['bold', 'italic', 'underline', 'strike'],
                  ['blockquote', 'code-block'],
                  [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                  [{ 'indent': '-1'}, { 'indent': '+1' }],
                  [{ 'header': [3, 4, 5, 6, false] }],
                  [{ 'align': [] }],
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

            fetchMessage: function() {
                var vm = this,
                    messageKey;
                vm.loading = true;
                
                var data = {
                    action: 'fpm-get-message-details',
                    project_id: vm.$route.params.projectid,
                    message_id: vm.$route.params.messageid,
                    nonce: fpm.nonce,
                };

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    vm.loading = false;
                    if ( resp.success ) {
                        vm.messageObject = resp.data[0];
                        vm.messageTitle = vm.messageObject.message_title;
                        vm.message = vm.messageObject.message;
                        vm.attachments = vm.messageObject.files;
                        vm.attachmentIDs = vm.messageObject.attachmentIDs;
                    }
                });
            },

            updateMessage: function() {
                var vm = this,
                    message,
                    messageID = vm.$route.params.messageid,
                    projectID = vm.$route.params.projectid,
                    data = {
                        action : 'fpm-insert-message',
                        nonce : fpm.nonce,
                        message_title: vm.messageTitle,
                        project_title: vm.messageObject.project_title,
                        message: vm.message,
                        project_id: projectID,
                        message_id: messageID,
                        attachments: vm.attachmentIDs
                    };

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    console.log(resp);
                    if ( resp.success ) {

                        // messageID = resp.data.messageInfo.ID;
                        // projectID = data.project_id;
                        vm.$router.push({ path: `/projects/${projectID}/messages/${messageID}` })
                        // resp.data.messageInfo.message = vm.message;
                        // vm.messages.unshift(resp.data.messageInfo);
                        // vm.message = '';
                        // vm.messageTitle = '';
                    } else {
                        // vm.message = resp.data;
                    }
                });
            },

            // fetchProjectInfo: function() {
            //     var vm = this;
                
            //     var data = {
            //         action: 'fpm-get-project',
            //         project_id: vm.$route.params.projectid,
            //         nonce: fpm.nonce,
            //     };

            //     jQuery.post( fpm.ajaxurl, data, function( resp ) {
            //         vm.loading = false;
            //         console.log(resp);
            //         if ( resp.success ) {
            //             vm.project = resp.data[0];
            //         }
            //     });
            // },
        },

        created() {
            this.fetchMessage();
            // var project_id,
            //     message_id,
            //     messageKey,
            //     vm = this,
            //     messageObject;

            // vm.$nextTick(() => {
            //     project_id = vm.$route.params.projectid;
            //     message_id = vm.$route.params.messageid;

            //     messageKey = 'm' + message_id + '-p' + project_id;
            //     messageObject = JSON.parse(localStorage.getItem(messageKey));

            //     vm.messageObj = messageObject;

            //     vm.messageTitle = messageObject.message_title;
            //     vm.message = messageObject.message;
            //     vm.attachments = messageObject.files;
            //     vm.attachmentIDs = messageObject.attachmentIDs;
            // });
        },

        mounted() {

        }
    }
</script>