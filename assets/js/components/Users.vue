<template>
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <router-link :to="'/projects/' + $route.params.projectid">Back to summary</router-link>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="box">
                    <div class="text-center">
                        <button class="button button-large button-primary"
                                @click="toggleAddForm"
                                v-if="!isShowAddForm">Add People</button>
                    </div>
                    <div class="add_form_style" v-if="isShowAddForm">
                        <div>
                            <input type="text" class="form-control" placeholder="Name" v-model="username">
                        </div>
                        <div>
                            <input type="text" class="form-control" placeholder="Email" v-model="email">
                        </div>
                        <div>
                            <input type="text" class="form-control" placeholder="Title" v-model="usertitle">
                        </div>
                        <br>
                        <div>
                            <!-- <input type="submit" class="button button-primary" v-model="localString.add_new" @click="createUser"> -->
                            <button class="button button-primary" @click="createUser">{{localString.add_new}}</button>
                            <button class="button button-default" @click="toggleAddForm">{{localString.cancel}}</button>
                        </div>
                    </div>
                    <br>
                    <h2 class="decorated"><span>People already on the project</span></h2>
                    <!-- <pre>
                        {{users}}
                    </pre> -->
                    <div class="loading" v-if="loading">
                        <p>Loading . . .</p>
                    </div>
                    <div class="row">
                        <div class="col-6" v-for="(user, uindex) in users" v-if="!loading">
                            <single-user :user="user" v-on:remove="removeUser" :index="uindex"></single-user>
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
                users: [],
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
            // fetchUsers: function() {
            //     var vm = this,
            //         todo,
            //         data = {
            //             action : 'fpm-get-users',
            //             nonce : fpm.nonce,
            //             project_id: vm.$route.params.projectid
            //         };

            //     jQuery.post( fpm.ajaxurl, data, function( resp ) {
            //         console.log(resp)
            //         if ( resp.success ) {
            //             vm.users = resp.data;
            //         } else {

            //         }
            //     });
            // },

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
                        console.log(resp);
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
                        title: vm.usertitle
                    };

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    console.log(resp)
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
                        userObj.display_name = vm.username;
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
            // vm.fetchUsers();

            store.getLocalizeString().then(function(resp){
                // console.log(resp);
                vm.localString = resp.data.actions;
            });

            projectid = vm.$route.params.projectid;

            store.fetchUsers( projectid ).then(function(resp){
                vm.users = resp.data;
            });

        },

        mounted() {
            // console.log('Component mounted.')
        }
    }
</script>