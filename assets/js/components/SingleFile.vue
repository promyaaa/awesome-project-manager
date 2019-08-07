<template>
    <div class="container">
        <project-nav>
            <span><i class="fa fa-angle-right"></i></span>
            <router-link :to="'/projects/' + $route.params.projectid + '/folders'" class="link-style t-d-none">
                {{i18n.docs_and_files}}
            </router-link>
            <span><i class="fa fa-angle-right"></i></span>
            <router-link :to="'/projects/' + $route.params.projectid + '/folders/' + $route.params.folderid" class="link-style t-d-none">
                {{folder.folder_title | truncate('15')}}
            </router-link>
        </project-nav>
        <div class="lists border-for-nav">
            <div class="row">
                <!-- <component-actions> -->
                    <!-- <bookmark :item="attachment" type="file"></bookmark> -->
                <!-- </component-actions> -->
                <div class="col-12">
                    <div class="text-center">
                        <div style="margin-top:40px;">
                            <files-type-display :file="attachment" type="folder"></files-type-display>
                        </div>
                        <div class="file-info">
                            <h3>{{attachment.title}}.{{attachment.extension}}</h3>
                            <p><i>{{i18n.posted_by}}</i> <strong>{{attachment.author_name}}</strong></p>
                        </div>
                        <div>
                            <a :href="attachment.url" download>
                                {{i18n.download}}
                            </a>
                        </div>
                    </div>
                    <comments :comments="attachment.comments" 
                                type="file"
                                :title="attachmentTitle"
                                :key="attachment.ID"></comments>
                </div>
                <!-- <help-component></help-component>   -->
            </div>
        </div>
    </div>
</template>

<script>
    import store from '../store';
    import Comments from './partials/CommentsComponent.vue';
    import ProjectNav from './partials/ProjectNavComponent.vue';
    import ComponentActions from './partials/ComponentActions.vue';
    // import Bookmark from './partials/BookmarkComponent.vue';
    import FilesTypeDisplay from './partials/FilesTypeDisplay.vue';
    import HelpComponent from './partials/HelpComponent.vue';
    export default{
        props: [],
        components: {
            ComponentActions,
            Comments,
            FilesTypeDisplay,
            ProjectNav,
            HelpComponent,
            // Bookmark
        },
        data() {
            return {
                loading: false,
                attachment: '',
                author: '',
                i18n: {},
                folder: '',
                showHelp: false
            }
        },
        computed: {
            isShowEdit: function() {
                var vm = this;
                return (vm.currentUser.roles.includes('administrator')) ||
                        (vm.currentUser.data.ID === vm.attachment.userID);
            },
            attachmentTitle() {
                return this.attachment.title +'.'+ this.attachment.extension;
            }
        },
        filters: {
            truncate: function(string, value) {
                var dotdot = '';
                if(!string)
                    string = '';
                if (string.length > value) {
                    dotdot = '...';
                }
                return string.substring(0, value) + dotdot;
            },
        },
        methods: {
            fetchFileDetails() {
                var vm = this;
                vm.loading = true;

                var data = {
                    action: 'fpm-get-file-details',
                    project_id: vm.$route.params.projectid,
                    folder_id: vm.$route.params.folderid,
                    file_id: vm.$route.params.fileid,
                    nonce: fpm.nonce,
                };

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    vm.loading = false;
                    if ( resp.success ) {
                        vm.folder = resp.data[0];
                        vm.attachment = vm.folder.attachment;
                    } else {
                        vm.$router.push({
                            path: `/?item=Folder&op=rf`
                        });
                    }
                });
            }
        },
        created() {
            var vm = this;
            vm.currentUser = fpm.currentUserInfo;
            store.setLocalization( 'fpm-get-single-file-local-data' ).then( function( data ) {
                vm.i18n = data;
            });
            vm.fetchFileDetails();
        }
    }
</script>

<style>
    .file-info {

    }
</style>