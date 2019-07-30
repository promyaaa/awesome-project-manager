<template>
    <div class="container">
        <project-nav>
            <span><i class="fa fa-angle-right"></i></span>
            <router-link :to="'/projects/' + $route.params.projectid + '/folders'" class="link-style t-d-none">
                {{i18n.docs_and_files}}
            </router-link>
        </project-nav>
        <div class="lists border-for-nav" style="min-height: 100px;">
            <!-- <archive-message v-if="isArchived">
                <span>{{i18n.this_folder_is}} <strong>{{i18n.archived}}</strong></span>
            </archive-message> -->
            <div class="row">
                <component-actions>
                    <li class="action-item" v-if="isShowEdit && !editForm && !isArchived" 
                        @click="toggleEdit">
                        <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                        {{i18n.edit}}
                    </li>
                    <li class="action-item" v-if="isShowEdit && !editForm && !isArchived" 
                        @click="deleteFolder">
                        <i class="fa fa-trash" aria-hidden="true"></i>
                        {{i18n.delete}}
                    </li>
                    <!-- <bookmark :item="folder" type="folder" v-if="!isArchived"></bookmark> -->
                    <!-- <archive :item="folder" type="folder"></archive> -->
                </component-actions>
                <div class="col-4">
                    <button @click="fileUpload" 
                            v-if="!editForm && !isArchived"
                            class="button button-default">+ {{i18n.add_files_to_folder}}</button>
                </div>
                <div class="col-4 text-center single-folder-title" v-if="!editForm">
                    <h3>{{folder.folder_title}}</h3>
                </div>
                <div v-else class="col-12">
                    <div class="text-center">
                        <input type="text" class="form-control" v-model="editText" style="width:90%">
                    </div>
                    <div class="left" style="margin-left:5%">
                        <button class="button button-primary" 
                                @click="updateFolder"
                                :disabled="updatingFolder">
                            <i v-if="updatingFolder" class="fa fa-refresh fa-spin mr-5"></i>
                            {{ i18n.update }}
                        </button>
                        <button class="button button-default" @click="cancelEdit">{{ i18n.cancel }}</button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <!-- <div class="loading" v-if="loading">
                        <p>Loading . . .</p>
                    </div> -->
                    <!-- <pre>
                        {{folder}}
                    </pre> -->
                    <div v-if="folder && !loading">
                        <div class="row">
                            <div class="col-3" v-for="(file, findex) in folder.files">
                                    <!-- {{file}} -->
                                    <div class="file-div">
                                        <div>
                                            <router-link :to="'/projects/' + $route.params.projectid + '/folders/' + $route.params.folderid + '/files/' + file.ID">
                                                <files-type-display :file="file" type="folder"></files-type-display>
                                            </router-link>
                                            <div class="text-center" style="margin-bottom:10px">
                                                <i>{{file.title | truncate('12')}}</i> 
                                            </div>
                                            <div class="file-actions text-center">
                                                <div class="action-item" v-tooltip title="download">
                                                    <a :href="file.url" download>
                                                        <i class="fa fa-download" style="color:#c4c4c4" aria-hidden="true"></i>
                                                    </a>
                                                </div>
                                                <div class="action-item" 
                                                    v-tooltip 
                                                    title="delete"
                                                    @click="deleteFile(file, findex)"
                                                    style="cursor:pointer">
                                                    <!-- <a> -->
                                                        <i class="fa fa-trash" style="color:#c4c4c4" aria-hidden="true"></i>
                                                    <!-- </a> -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                
                            </div>
                        </div>
                    </div>
                    <comments :comments="folder.comments" 
                                type="folder"
                                :archive="folder.is_archive"
                                :title="folder.folder_title"
                                :key="folder.ID">
                    </comments>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import store from '../store';
    import FilesTypeDisplay from './partials/FilesTypeDisplay.vue';
    import ProjectNav from './partials/ProjectNavComponent.vue';
    // import Bookmark from './partials/BookmarkComponent.vue';
    // import ArchiveMessage from './partials/ArchiveMessageComponent.vue';
    // import Archive from './partials/ArchiveComponent.vue';
    import Comments from './partials/CommentsComponent.vue';
    import ComponentActions from './partials/ComponentActions.vue';
    export default{
        props: [],
        components: {
            FilesTypeDisplay,
            ProjectNav,
            ComponentActions,
            // Bookmark,
            Comments,
            // Archive,
            // ArchiveMessage
        },
        data() {
            return {
                i18n: {},
                loading: false,
                project: '',
                folder: '',
                existingFiles: [],
                editForm: false,
                editText: '',
                updatingFolder: false,
            }
        },
        computed: {
            isShowEdit: function() {
                var vm = this;
                return (vm.currentUser.roles.includes('administrator')) ||
                        (vm.currentUser.data.ID === vm.folder.userID);
            },
            isArchived() {
                return +this.folder.is_archive;
            }
        },
        filters:{
            truncate: function(string, value) {
                var dotdot = '';
                if(!string)
                    string = '';
                if (string.length > value) {
                    dotdot = '...';
                }
                return string.substring(0, value) + dotdot;
            }
        },
        methods: {
            toggleEdit() {
                this.editForm = true;
                this.editText = this.folder.folder_title;
                // Event.$emit('toggle-actions');
            },
            cancelEdit() {
                this.editForm = false;
            },
            deleteFolder() {
                if (confirm("Are you sure ??")) {
                    var vm = this,
                        projectID = vm.$route.params.projectid,
                        data = {
                            action : 'fpm-delete-folder',
                            nonce : fpm.nonce,
                            folder_id: vm.$route.params.folderid,
                            project_id: projectID,
                            folder_title: vm.folder.folder_title
                        };

                    jQuery.post( fpm.ajaxurl, data, function( resp ) {
                        if ( resp.success ) {
                            vm.$router.push({
                                path: `/projects/${projectID}/folders?item=folder&op=ds`
                            });
                        }
                        // Event.$emit('flash', {
                        //     message: resp.data.message,
                        //     flashtype:'danger',
                        //     iconfont: 'fa-question-circle',
                        //     duration: 2000
                        // });
                    });
                }
            },
            deleteFile( file, findex ) {
                if (confirm("Are you sure, you want to delete this file ??")) {
                    var vm = this,
                        index,
                        folder,
                        data = {
                            action : 'fpm-insert-folder',
                            nonce : fpm.nonce,
                            project_id: vm.$route.params.projectid,
                            folder_id: vm.$route.params.folderid,
                            file_id: file.ID,
                            folder_title: vm.folder.folder_title,
                            file_title: file.title +'.'+ file.extension,
                            ac_type: 'delete_file'
                        };
                        
                        vm.folder.attachmentIDs.splice( findex, 1 )
                        data.attachments = vm.folder.attachmentIDs;
                    jQuery.post( fpm.ajaxurl, data, function( resp ) {
                        if ( resp.success ) {
                            vm.folder.files.splice( findex, 1 );
                            // Event.$emit('flash', {
                            //     message: resp.data.message,
                            //     flashtype:'success',
                            //     iconfont: 'fa-check',
                            //     duration: 2000
                            // });
                        } else {
                            var files = vm.folder.files;
                            vm.folder.attachmentIDs = [];
                            for(var j=0; j<files.length; j++) {
                                vm.folder.attachmentIDs.push(files[j].ID);
                            }
                            // Event.$emit('flash', {
                            //     message: resp.data.message,
                            //     flashtype:'danger',
                            //     iconfont: 'fa-question-circle',
                            //     duration: 2000
                            // });
                        }
                    });
                }
            },
            updateFolder() {
                var vm = this,
                folder,
                data = {
                    action : 'fpm-insert-folder',
                    nonce : fpm.nonce,
                    folder_title: vm.editText,
                    folder_id: vm.$route.params.folderid,
                    project_id: vm.$route.params.projectid,
                    ac_type: 'update_folder',
                    attachments: vm.folder.attachmentIDs
                };

                vm.updatingFolder = true;

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    if ( resp.success ) {
                        vm.updatingFolder = false;
                        vm.folder.folder_title = vm.editText;
                        vm.editText = '';
                        vm.editForm = false;
                        // Event.$emit('flash', {
                        //     message: resp.data.message,
                        //     flashtype:'success',
                        //     iconfont: 'fa-check'
                        // });
                    } else {
                        vm.updatingFolder = false;
                    }
                });
            },
            fetchFolderDetails() {
                var vm = this;
                vm.loading = true;

                var data = {
                    action: 'fpm-get-folder-details',
                    project_id: vm.$route.params.projectid,
                    folder_id: vm.$route.params.folderid,
                    nonce: fpm.nonce,
                };

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    vm.loading = false;
                    if ( resp.success ) {
                        vm.folder = resp.data[0];
                    } else {
                        vm.$router.push({
                            path: `/?item=Folder&op=rf`
                        });
                    }
                });
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
                    
                    isAllowedType = mime_array.includes(attachment.mime);

                    if(isAllowedType) {
                        if(vm.folder.attachmentIDs.includes(attachment.id.toString())) {
                            // Event.$emit('flash', {
                            //     message: 'file already exists',
                            //     flashtype:'success',
                            //     iconfont: 'fa-check'
                            // });
                        } else {
                            vm.addFile(attachment);
                        }
                    }
                });
                file_frame.open();
            },
            addFile(attachment) {
                var vm = this,
                    folder,
                    data;

                vm.folder.attachmentIDs.push(attachment.id.toString());

                data = {
                    action : 'fpm-insert-folder',
                    nonce : fpm.nonce,
                    project_id: vm.$route.params.projectid,
                    folder_id: vm.$route.params.folderid,
                    attachments: vm.folder.attachmentIDs,
                    folder_title: vm.folder.folder_title,
                    ac_type: 'add_file',
                    file_id : attachment.id
                };

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    if ( resp.success ) {
                        attachment.ID = attachment.id;
                        vm.folder.files.push(attachment);

                        // Event.$emit('flash', {
                        //     message: resp.data.message,
                        //     flashtype:'success',
                        //     iconfont: 'fa-check'
                        // });
                    }
                });
            }
        },
        created() {
            var vm = this;
            vm.currentUser = fpm.currentUserInfo;
            vm.fetchFolderDetails();
            store.setLocalization( 'fpm-get-folder-local-data' ).then( function( data ) {
                vm.i18n = data;
            });
        }
    }
</script>

<style>
    .single-folder-title {
        border-bottom: 2px solid grey;
        margin-bottom:35px;
    }
    .file-div {
        padding: 30px 30px 15px;
        border: 1px solid #eee;
        border-radius:4px;
        min-height: 170px;
    }
    .file-actions .action-item {
        display: inline-block;
        padding: 5px 10px;
        border: 1px solid #eee;
        /*cursor: pointer;*/
    }
    /*.file-action {
        margin-top: 8px;
        bottom: 20px;
        border: 1px solid #eee;
        left: 40%;
        position: absolute;
        padding: 6px 10px;
        cursor: pointer;
    }*/
</style>