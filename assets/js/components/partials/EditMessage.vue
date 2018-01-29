<template>
    <div>
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <router-link :to="'/projects/' + $route.params.projectid " class="link-style inline-block" tag="h3">
                        <a>{{messageObject.project_title}}</a>
                    </router-link>
                    <router-link :to="'/projects/' + $route.params.projectid + '/messages/'" class="link-style inline-block" tag="h4">
                        <a><i class="fa fa-long-arrow-right p-l-10 p-r-10" aria-hidden="true"></i>{{ i18n.message_label }}</a>
                    </router-link>

                    <router-link :to="'/projects/' + $route.params.projectid + '/messages/' + $route.params.messageid" class="link-style inline-block" tag="h4">
                        <a><i class="fa fa-long-arrow-right p-l-10 p-r-10" aria-hidden="true"></i>{{messageObject.message_title}} </a>
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
                                       required
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
                                <button class="button button-primary"
                                        @click.prevent="updateMessage"
                                        >{{ i18n.update }}</button>
                                <router-link :to="'/projects/' + $route.params.projectid + '/messages/' + messageObject.ID" class="button button-default">
                                    {{ i18n.cancel }}
                                </router-link>
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
                i18n:{},
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
                        vm.$router.push({ path: `/projects/${projectID}/messages/${messageID}` })
                    }
                });
            }
        },

        created() {
            var vm = this;
            store.setLocalization( 'fpm-get-edit-message-local-data' ).then( function( data ) {
                vm.i18n = data;
            });

            this.fetchMessage();
        }
    }
</script>