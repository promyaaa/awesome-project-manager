<template>
    <div class="container">
        <!-- <div class="row">
            <div class="col-1"></div>
            <div class="col-10">
                <div v-if="project" class="project-navigation">
                    <router-link :to="'/projects/' + $route.params.projectid" tag="h3" class="link-style">
                        <a>{{project.project_title}}</a>
                    </router-link>
                </div>
            </div>
        </div> -->
        <project-nav v-on:get-project="setProject"></project-nav>
        <div class="lists border-for-nav">
            <div class="row">
                <div class="col-12">
                    <div class="row" v-if="message">
                        <div class="col-12">
                            <div class="m-default m-success">
                                <p><strong>{{message}}</strong></p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 text-center">
                            <h3>{{ i18n.header_label }}<i style="color:#6d6d6d">{{project.project_title}}</i></h3>
                            <p style="font-size: 14px;">{{ i18n.header_note }}</p>
                        </div>
                    </div>
                    <div>
                        <div class="text-center">
                            <button class="button button-large button-primary"
                                    @click="toggleAddForm"
                                    v-if="!isShowAddForm">{{ i18n.add_btn_text }}</button>
                        </div>
                        <div class="add_form_style" v-if="isShowAddForm">
                            <div>
                                <input type="text" class="form-control" :placeholder="i18n.name_placeholder" v-model="username" required>
                            </div>
                            <div>
                                <input type="text" class="form-control" :placeholder="i18n.email_placeholder" v-model="email" required>
                            </div>
                            <div>
                                <input type="text" class="form-control" :placeholder="i18n.title_placeholder" v-model="usertitle">
                            </div>
                            <br>
                            <div>
                                <!-- <input type="submit" class="button button-primary" v-model="localString.add_new" @click="createUser"> -->
                                <button class="button button-primary" 
                                        @click="createUser"
                                        :disabled="addingUser">
                                    <i v-if="addingUser" class="fa fa-refresh fa-spin mr-5"></i>{{i18n.add_new}}
                                </button>
                                <button class="button button-default" @click="toggleAddForm">{{ i18n.cancel }}</button>
                            </div>
                        </div>
                        <br>
                        <h2 class="decorated"><span>{{ i18n.decorated_heading }}</span></h2>
                        
                        <div class="text-center" v-if="loading">
                            <i class="fa fa-refresh fa-spin fa-3x fa-fw" aria-hidden="true"></i>
                            <p>{{ i18n.loading }}</p>
                        </div>
                        <div class="row">
                            <div class="col-6" v-for="(user, uindex) in users" v-if="!loading">
                                <single-user :user="user" v-on:remove="removeUser" :index="uindex" :i18n="i18n"></single-user>
                            </div>
                            <div class="col-12 text-center" v-if="users.length < totalUsers">
                                <button class="button" style="margin-top: 30px;" @click="loadMoreUsers">Load More</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import store from '../store';
    import SingleUser from './partials/SingleUserComponent.vue';
    import ProjectNav from './partials/ProjectNavComponent.vue';
    export default {
        components: {
            SingleUser,
            ProjectNav
        },
        data() {
            return {
                i18n: {},
                users: [],
                totalUsers: '',
                project: '',
                username: '',
                email: '',
                loading: false,
                localString: '',
                selected: '',
                usertitle: '',
                isShowAddForm: false,
                message: '',
                addingUser: false,
            }
        },
        methods: {
            setProject( project ) {
                this.project = project;
            },
            removeUser( index ) {
                if (confirm("Are you sure ??")) {
                    var vm = this,
                        projectid = vm.$route.params.projectid,
                        localUsersKey = projectid + '-users',
                        data = {
                            action : 'fpm-remove-user',
                            nonce : fpm.nonce,
                            user_id : vm.users[index].ID,
                            project_id : projectid
                        };
                        
                    jQuery.post( fpm.ajaxurl, data, function( resp ) {
                        
                        if ( resp.success ) {
                            vm.users.splice(index, 1);
                            vm.totalUsers = vm.totalUsers - 1;
                            localStorage.removeItem(localUsersKey);
                            localStorage.setItem(localUsersKey, JSON.stringify(vm.users));

                        } else {

                        }
                    });
                }
            },

            toggleAddForm: function() {
                this.isShowAddForm = !this.isShowAddForm;
            },

            loadMoreUsers() {
                var vm = this;
                // vm.loadMore = true;
                var data = {
                    action: 'fpm-load-more-users',
                    nonce: fpm.nonce,
                    project_id: vm.$route.params.projectid,
                    offset: vm.users.length
                };

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    // vm.loadMore = false;
                    if ( resp.success ) {
                        for(var i = 0; i < resp.data.length; i++ ) {
                            vm.users.push(resp.data[i]);
                        }
                    }
                });
            },

            createUser: function() {
                var vm = this,
                    projectid = vm.$route.params.projectid,
                    localUsersKey = projectid + '-users',
                    data = {
                        action : 'fpm-insert-user',
                        nonce : fpm.nonce,
                        user_name: vm.username,
                        email: vm.email,
                        project_id: projectid,
                        project_title: vm.project.project_title,
                        title: vm.usertitle
                    };

                if(!vm.email) {
                    return;
                }

                vm.addingUser = true;

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    vm.addingUser = false;
                    if ( resp.success ) {
                        if ( !resp.data.user ) {
                            vm.username = '';
                            vm.email = '';
                            vm.usertitle = '';
                            return;
                        }
                        var userObj = {};
                        userObj.ID = resp.data.user.ID;
                        userObj.avatar_url = resp.data.user.avatar_url;
                        userObj.display_name = resp.data.user.user_name;
                        userObj.user_email = vm.email;
                        userObj.title = vm.usertitle;
                        vm.message = 'Credentials has been sent to '+ userObj.display_name +'\'s email address';

                        localStorage.removeItem(localUsersKey);

                        vm.users.push(userObj);
                        vm.username = '';
                        vm.email = '';
                        vm.usertitle = '';

                    } else {
                        // vm.message = resp.data;
                    }
                });
            }
        },

        created() {
            var vm = this,
                projectid;

            vm.loading = true;
            store.setLocalization( 'fpm-get-users-local-data' ).then( function( data ) {
                vm.i18n = data;
            });

            projectid = vm.$route.params.projectid;

            store.fetchUsers( projectid ).then(function(resp){
                vm.loading = false;
                vm.totalUsers = resp.data[0].user_count;
                vm.users = resp.data;
            });

            // vm.fetchProjectInfo();

        },

        mounted() {
            // console.log('Component mounted.')
        }
    }
</script>

<style>
    .m-default {
        -webkit-box-shadow: 0 1px 1px 0 rgba( 0, 0, 0, 0.1 );
        box-shadow: 0 1px 1px 0 rgba( 0, 0, 0, 0.1 );
        padding: 1px 12px;
    } 
    .m-success {
        border-left: 4px solid #46b450;
    }
    .m-danger {
        border-left: 4px solid #D54E21;
    }
    .m-info {
        border-left: 4px solid #00A0D2;
    }
    .m-warning {
        border-left: 4px solid #FFBA00;
    }
    .user-info {
        padding-left: 15px;
    }
    .user-info .fa {
        font-size: 13px;
        color: #b5b5b5;
    }
    .user-info span.info{
        display: block;
    }
</style>