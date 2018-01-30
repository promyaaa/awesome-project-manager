<template>
    <div class="container">
        <div class="row">
            <div class="col-1"></div>
            <div class="col-10">
                <div v-if="project" class="project-navigation">
                    <router-link :to="'/projects/' + $route.params.projectid" tag="h3" class="link-style">
                        <a>{{project.project_title}}</a>
                    </router-link>
                </div>
            </div>
        </div>
        <div class="row box">
            <div class="col-12">
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
                            <button class="button button-primary" @click="createUser">{{i18n.add_new}}</button>
                            <button class="button button-default" @click="toggleAddForm">{{ i18n.cancel }}</button>
                        </div>
                    </div>
                    <br>
                    <h2 class="decorated"><span>{{ i18n.decorated_heading }}</span></h2>
                    
                    <div class="loading" v-if="loading">
                        <p>{{ i18n.loading }}</p>
                    </div>
                    <div class="row">
                        <div class="col-6" v-for="(user, uindex) in users" v-if="!loading">
                            <single-user :user="user" v-on:remove="removeUser" :index="uindex" :i18n="i18n"></single-user>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
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

<script>
    import SingleUser from './partials/SingleUserComponent.vue';
    import store from '../store';
    export default {
        components: {
            SingleUser
        },
        data() {
            return {
                i18n: {},
                users: [],
                project: '',
                username: '',
                email: '',
                loading: false,
                localString: '',
                selected: '',
                usertitle: '',
                isShowAddForm: false
            }
        },
        methods: {

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
                        // console.log(resp);
                        if ( resp.success ) {

                            vm.users.splice(index, 1);
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

            fetchProjectInfo: function() {
                var vm = this;
                // vm.loading = true;

                var data = {
                    action: 'fpm-get-project',
                    project_id: vm.$route.params.projectid,
                    is_summary: true,
                    nonce: fpm.nonce,
                };

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    if ( resp.success ) {
                        vm.project = resp.data;
                    } else {
                        vm.$router.push({
                            path: `/?type=project&info=notfound`
                        });
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

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    // console.log(resp)
                    if ( resp.success ) {
                        if ( !resp.data.user ) {
                            vm.username = '';
                            vm.email = '';
                            vm.usertitle = '';
                            // show message
                            // TODO: create message component
                            return;
                        }
                        var userObj = {};
                        userObj.ID = resp.data.user.ID;
                        userObj.avatar_url = resp.data.user.avatar_url;
                        userObj.display_name = resp.data.user.user_name;
                        userObj.user_email = vm.email;
                        userObj.title = vm.usertitle;

                        localStorage.removeItem(localUsersKey);

                        vm.users.push(userObj);
                        vm.username = '';
                        vm.email = '';
                        vm.usertitle = '';

                    } else {
                        vm.message = resp.data;
                    }
                });
            }
        },

        created() {
            var vm = this,
                projectid;

            store.setLocalization( 'fpm-get-users-local-data' ).then( function( data ) {
                vm.i18n = data;
            });

            // store.getLocalizeString().then(function(resp){
            //     vm.localString = resp.data.actions;
            // });

            projectid = vm.$route.params.projectid;

            store.fetchUsers( projectid ).then(function(resp){
                vm.users = resp.data;
            });

            vm.fetchProjectInfo();

        },

        mounted() {
            // console.log('Component mounted.')
        }
    }
</script>