<template>
    <div>
        <div class="container">
            <!-- <div class="row">
                <div class="col-12 text-center">
                    <router-link :to="'/projects/' + $route.params.projectid " class="link-style inline-block" tag="h3">
                        <a>{{project.project_title}}</a>
                    </router-link>
                    <router-link :to="'/projects/' + $route.params.projectid + '/messages/'" class="link-style inline-block" tag="h4">
                        <a><i class="fa fa-long-arrow-right p-l-10 p-r-10" aria-hidden="true"></i>{{ i18n.message_label }}</a>
                    </router-link>
                </div>
            </div> -->
            <project-nav v-on:get-project="setProject">
                <span><i class="fa fa-angle-right"></i></span>
                <router-link :to="'/projects/' + $route.params.projectid + '/messages'" class="link-style t-d-none">
                    {{ i18n.message_label }}
                </router-link>
            </project-nav>
            <div class="lists border-for-nav">
                <div class="row">
                    <div class="col-12">
                        <div>
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
                                
                                <file-upload
                                    :i18n="i18n"
                                    v-on:attach="updateAttachments"
                                    v-on:remove="removeAttachment"
                                    :attachments="attachments"></file-upload>
                                
                                <div class="new-message-action">
                                    <button class="button button-primary" @click.prevent="createMessage">
                                        {{ i18n.post_new_msg_btn }}
                                    </button>
                                    <router-link :to="'/projects/' + $route.params.projectid + '/messages'" class="button button-default">
                                        {{ i18n.cancel }}
                                    </router-link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
    .new-message-action {
        margin-top: 10px
    }
</style>

<script>
    import store from '../../store';
    import { VueEditor } from 'vue2-editor'
    import FileUpload from './FileUploadComponent.vue';
    import ProjectNav from './ProjectNavComponent.vue';
    export default {
        components: {
            VueEditor,
            FileUpload,
            ProjectNav
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
                project: '',
                // content: '',
                customToolbar : [
                  ['bold', 'italic', 'underline', 'strike'],
                  ['blockquote', 'code-block'], 
                  [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                  [{ 'indent': '-1'}, { 'indent': '+1' }],
                  [{ 'header': [3, 4, 5, 6, false] }],
                  [{ 'align': [] }]
                ]

            }
        },

        methods: {
            setProject( project ) {
                this.project = project;
            },
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

                if ( !vm.messageTitle ) {
                    return;
                }

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    if ( resp.success ) {
                        messageID = resp.data.messageInfo.ID;
                        projectID = data.project_id;
                        vm.$router.push({
                            path: `/projects/${projectID}/messages/${messageID}`
                        });
                    } else {
                        // vm.message = resp.data;
                    }
                });
            }
        },

        created() {
            var vm = this;
            store.setLocalization( 'fpm-get-new-message-local-data' ).then( function( data ) {
                vm.i18n = data;
            });
        },

        mounted() {

        }
    }
</script>