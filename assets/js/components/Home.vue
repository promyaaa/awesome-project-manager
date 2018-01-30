<template>
    <div>
        <div class="container">

            <div id="pageparentdiv" class="postbox">
                <div class="inside">
                    <div class="row">
                        <div class="col-6 text-center user-info-sections">
                            <img :src="currentUser.data.avatar_url" class="small-round-image">
                            <div class="current-user-name">
                                <h3>{{currentUser.data.display_name}}</h3>
                            </div>
                        </div>

                        <div class="col-6 user-quick-link">
                            <div>
                                <ul>
                                    <router-link to="/my/assignments" tag="li" class="link-style">
                                        <a>{{ i18n.my_assignments }}</a>
                                    </router-link>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <h2 class="decorated"><span>{{ i18n.projects }}</span></h2>
                </div>
                <div class="col-6">
                </div>
                <div class="col-6">
                    <a class="button button-primary right" @click.prevent="toggleProjectForm" v-if="!isShowProjectForm">+ {{ i18n.add_new_project }}</a>
                </div>
            </div>
            <div class="row" v-if="isNoProject">
                <div class="col-12">
                    <p><strong>{{ i18n.no_prject_found_message }}</strong></p>
                </div>
            </div>
            <div class="row" v-if="isShowProjectForm">
                <div class="col-12">
                    <div class="add_form_style" style="margin: 5px;">
                        <form>
                            <div class='section'>
                                <input type="text" name="project_title" v-model="projectTitle" class="form-control" :placeholder="i18n.project_title_placeholder" v-focus @keyup.esc="toggleProjectForm">
                                <textarea class="form-control" name="project_desc" v-model="projectDesc" rows="3" :placeholder="i18n.project_description_placeholder"></textarea>
                            </div>
                            <div class="action">
                                <button class="button button-primary" @click.prevent="createProject">{{ i18n.create_project_label }}</button>
                                <button class="button button-default" @click="toggleProjectForm">{{ i18n.cancel_project_label }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12" v-if="loading">
                    <div class="loading">
                        <h2>{{ i18n.loading }}</h2>
                    </div>
                </div>

                <div class="col-4" v-for="project in projects" v-if="projects.length > 0 && !loading">
                    <div class="project">
                        <router-link :to="'/projects/' + project.ID" tag="h3" class="link-style">
                            <div class="ellipsis-80">
                                <a class="">{{project.project_title}}</a>
                            </div>
                        </router-link>

                        <p class="ellipsis-90">{{project.project_desc}}</p>

                        <div class="user-avatars">
                            <img :src="user.avatar_url" v-for="user in project.users" class="small-round-image" width="32" height="32">
                            <span v-if="project.user_count > 5" class="more-user">
                                <a>+{{project.user_count - 5}}</a>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <div class="row" v-if="projects.length < projectCount">
                <div class="col-12 text-center">
                    <button class="button button-default" @click="loadMoreProjects">{{ i18n.load_more }}</button>
                </div>
            </div>
        </div>

    </div>

</template>

<style>
    .current-user-name h3{
        margin: 5px 0px;
    }

    .user-info-sections {
        box-sizing: border-box;
        border-right: 1px solid #eee;
    }

    .user-quick-link div {
        padding-left: 30px;
    }

    .user-quick-link div ul{
        list-style-type: default;
    }

    .user-quick-link div ul li:before{
        content: '\f178';
        color: #afafaf;
        display: inline-block;
        font: normal normal normal 14px/1 FontAwesome;
        margin-right: 10px;
    }
    .project {
        background-color: #fff;
        margin:5px;
        padding-bottom: 15px;
        position: relative;
    }

    .project .project-settings{
        position: absolute;
        top:10px;
        right: 10px;
        cursor: pointer;
    }

    .project .project-settings a.setting-icon{
        color: #afafaf;

    }
    .project h3{
        padding: 10px 15px;
        border-bottom: 1px solid #eee;
    }

    .project h3 a{
        font-size: 15px;
        color: #333;
    }

    .project p{
        padding: 0 15px;
    }
    .project .user-avatars{
        padding: 0px 15px;
    }

    .ellipsis-90 {
        white-space: nowrap;
        width: 90%;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .ellipsis-80 {
        white-space: nowrap;
        width: 80%;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .ellipsis-70 {
        white-space: nowrap;
        width: 70%;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .ellipsis-99 {
        white-space: nowrap;
        width: 99%;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .more-user {
        float: right;
        margin-top: 7px;
    }
</style>

<script>
    import store from '../store';
    export default {
        data() {
            return {
                i18n: {},
                projects: [],
                isShowProjectForm: false,
                projectTitle: '',
                projectDesc: '',
                loading: false,
                projectCount: '',
                loadMore: false
            }
        },

        directives: {
            focus: {
                inserted: function (el) {
                    el.focus()
                }
            }
        },

        computed: {
            isNoProject: function() {
                return this.projects.length < 1 && !this.isShowProjectForm && !this.loading;
            }
        },

        methods: {
            toggleProjectForm: function() {
                this.isShowProjectForm = !this.isShowProjectForm;
            },

            loadMoreProjects() {
                var vm = this;
                vm.loadMore = true;
                var data = {
                    action: 'fpm-load-more-projects',
                    nonce: fpm.nonce,
                    offset: vm.projects.length
                };

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    vm.loadMore = false;
                    if ( resp.success ) {
                        for(var i = 0; i < resp.data.length; i++ ) {
                            vm.projects.push(resp.data[i]);
                        }
                    }
                });
            },

            fetchProjects: function() {
                var vm = this;
                vm.loading = true;
                var data = {
                    action: 'fpm-get-projects',
                    nonce: fpm.nonce,
                };

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    vm.loading = false;
                    // console.log(resp);
                    if ( resp.success ) {
                        vm.projects = resp.data;
                    }
                });
            },

            fetchProjectCount: function() {
                var vm = this;
                vm.loading = true;
                var data = {
                    action: 'fpm-get-project-count',
                    nonce: fpm.nonce,
                };

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    // console.log(resp);
                    if ( resp.success ) {
                        vm.projectCount = resp.data;
                    }
                });
            },

            createProject: function() {
                var vm = this,
                project,
                user,
                data = {
                    action : 'fpm-insert-project',
                    nonce : fpm.nonce,
                    title: vm.projectTitle,
                    description: vm.projectDesc
                };

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    // console.log(resp);
                    if ( resp.success ) {
                        resp.data.project.project_title = vm.projectTitle;
                        resp.data.project.project_desc = vm.projectDesc;
                        user = {
                            avatar_url: vm.currentUser.data.avatar_url
                        }
                        resp.data.project.users = [user];
                        vm.projects.push(resp.data.project);
                        vm.projectTitle = '';
                        vm.projectDesc = '';
                    }
                });
            }
        },

        created() {
            var self = this;
            store.setLocalization( 'fpm-get-home-local-data' ).then( function( data ) {
                self.i18n = data;
            });

            this.fetchProjects();
            this.fetchProjectCount();
            this.currentUser = fpm.currentUserInfo;
        }
    }
</script>