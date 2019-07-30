<template>
    <div class="container">
        <project-nav></project-nav>
        <div class="lists border-for-nav">
            <div class="row">
                <div class="col-4">
                    <button class="button button-default" @click.prevent="toggleFolderForm" v-if="!isShowFolderForm">
                        {{i18n.make_folder}}
                    </button>
                </div>
                <div class="col-4 text-center" style="border-bottom: 2px solid grey;margin-bottom:35px;">
                    <h3>{{i18n.docs_and_files}}</h3>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div v-if="isShowFolderForm" class="add_form_style">
                        <div>
                            <input type="text"
                                name="list_title"
                                v-model="folderTitle"
                                class="form-control"
                                :placeholder="i18n.folder_title"
                                @keyup.enter="createFolder"
                                @keyup.esc="toggleFolderForm"
                                v-focus>
                        </div>
                        <div class="action">
                            <button class="button button-primary" @click.prevent="createFolder"
                                    >
                                {{i18n.create}}
                            </button>
                            <button class="button button-default" @click="toggleFolderForm">
                                {{i18n.cancel}}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="text-center" v-if="loading">
                        <i class="fa fa-refresh fa-spin fa-3x fa-fw" aria-hidden="true"></i>
                    </div>

                    <div v-if="folders.length < 1 && !loading">
                        <h4>{{i18n.no_folder_message}}</h4>
                    </div>
                    <div v-if="folders.length > 0 && !loading">
                        <div>
                            <div class="row">
                                <div class="col-3 animated fadeIn" v-for="(folder, mindex) in folders" :key="folder.ID">
                                    <div class="folder">
                                        <div class="folder-title">
                                            <router-link :to="'/projects/' + $route.params.projectid + '/folders/' + folder.ID" tag="h4">
                                                <a>{{folder.folder_title | truncate('12')}}</a>
                                            </router-link>
                                        </div>
                                        <div class="text-center">
                                            <!-- <img :src="assetsDistPath+imageSource" alt="" width="80" height="80"> -->
                                            <i class="fa fa-folder-open-o fa-4x"></i>
                                        </div>
                                        <div class="row text-center">
                                            <div class="col-12">
                                                <span><i>{{i18n.created_by}} <strong>{{folder.user_name}}</strong></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" v-if="folders.length < folderCount">
                        <div class="col-12 text-center">
                            <button class="button button-default" @click="fetchFolders(true)">
                                {{i18n.load_more_btn}}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" style="border-top: 1px solid #eee" v-if="+aFolderCount > 0">
                <router-link :to="'/projects/'+$route.params.projectid+'/folders/archive'" tag="p" style="padding-left:20px">
                    <a>{{aFolderCount}} {{i18n.archived_folders}}</a>
                </router-link>
            </div>
        </div>
    </div>
</template>

<script>
    import store from '../store';
    import ProjectNav from './partials/ProjectNavComponent.vue';
    export default{
        props: [],
        components: {
            ProjectNav
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
        data() {
            return {
                i18n: {},
                isShowFolderForm: false,
                folderTitle: '',
                project: {},
                folders: [],
                folderCount: '',
                aFolderCount: '',
                loading: false,
                loadMoreFolders: false,
                currentUser: '',
                // assetsDistPath: fpm.plugin_dir + 'assets/dist/',
                // imageSource: require('./icons/if_folder_icon.png')
                // imageSource: require('./icons/folder-icon-one.png')
                // foldericon: FolderIcon
            }
        },
        directives: {
            focus: {
                inserted: function (el) {
                    el.focus()
                }
            }
        },
        methods: {
            toggleFolderForm() {
                this.isShowFolderForm = !this.isShowFolderForm;
            },
            fetchFolders: function(isLoadMore) {
                var vm = this,
                    i;

                if(!isLoadMore) {
                    vm.loading = true;
                }

                var data = {
                    action: 'fpm-get-folders',
                    project_id: vm.$route.params.projectid,
                    nonce: fpm.nonce,
                };

                if(isLoadMore) {
                    data.offset = vm.folders.length;
                }

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    if ( resp.success ) {
                        for( i = 0; i < resp.data.length; i++ ) {
                            vm.folders.push(resp.data[i]);
                        }
                        if(!isLoadMore) {
                            vm.loading = false;
                            vm.folderCount = resp.data[0].folder_count;
                            vm.aFolderCount = resp.data[0].a_folder_count;
                        }
                    }
                    vm.loading = false;
                });
            },
            createFolder() {
                var vm = this,
                folder,
                data = {
                    action : 'fpm-insert-folder',
                    nonce : fpm.nonce,
                    folder_title: vm.folderTitle,
                    project_title: vm.project.project_title,
                    project_id: vm.$route.params.projectid,
                    ac_type: 'create_folder'
                };

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    if ( resp.success ) {
                        resp.data.folder.folder_title = vm.folderTitle;
                        resp.data.folder.user_name = vm.currentUser.data.display_name;
                        vm.folders.unshift(resp.data.folder);
                        vm.folderTitle = '';
                        // Event.$emit('flash', {
                        //     message: resp.data.message,
                        //     flashtype:'success',
                        //     iconfont: 'fa-check',
                        //     duration: 2000
                        // });
                    }
                });
            }
        },
        created() {
            var vm = this;
            store.setLocalization( 'fpm-get-folder-local-data' ).then( function( data ) {
                vm.i18n = data;
            });
            vm.fetchFolders();
            vm.currentUser = fpm.currentUserInfo;
        },
    }
</script>

<style>
    .folder {
        width: 100%;
        height: 200px;
        position: relative;
        background-color: #ffffff;
        border-radius: 5px;
        border: 1px solid #E5E5E5;
    }
    .folder i.fa-4x {
        color: #267cb5;
        margin-bottom: 20px;
    }
    .folder:before {
        content: '';
        width: 52%;
        height: 23px;
        border-radius: 0px 5px 0px 40px;
        background-color: #E5E5E5;
        position: absolute;
        top: 0px;
        right: 0px;
    }
    .folder-title {
        padding-top: 5px;
        text-align: center;
        font-weight: bold;
    }
</style>