<template>
    <div>
        <div class="container summary-section">
            <div class="row">
                <div class="col-12">
                    <div class="text-center project-info">
                        <h1><strong>{{project.project_title}}</strong></h1>
                        <span>{{project.project_desc}}</span>

                        <!-- <span class="dropdown project-settings show-edit" v-if="isShowEdit">
                            <a data-target="#" class="setting-icon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Settings">
                                <i class="fa fa-gear" aria-hidden="true" style="font-size:15px;"></i>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                <router-link :to="'/projects/' + $route.params.projectid + '/edit'"
                                    class="link-style"
                                    tag="li">
                                    <a><i class="fa fa-edit p-r-10" aria-hidden="true"></i>{{ i18n.edit_info }}</a>
                                </router-link>
                                <li role="separator" class="divider"></li>
                                <router-link :to="'/projects/' + $route.params.projectid + '/status'"
                                    class="link-style"
                                    tag="li">
                                    <a><i class="fa fa-trash p-r-10" aria-hidden="true"></i>{{ i18n.delete }}</a>
                                </router-link>
                            </ul>
                        </span> -->
                    </div>
                </div>
                <div class="col-12 text-center" v-if="loading">
                    <i class="fa fa-refresh fa-spin fa-3x fa-fw" aria-hidden="true"></i>
                    <p>Loading. . .</p>
                </div>
                <div class="col-12" v-if="!loading">
                    <div class="users-summary">
                        <div style="display: inline-block">
                        
                            <img :src="user.avatar_url" v-for="user in users" alt="" class="small-round-image">
                        </div>
                        <div v-if="project.user_count > 10" style="display: inline-block;position: absolute;padding-top: 15px;padding-left:5px;">
                            <a>+{{project.user_count - 10}} {{ i18n.people }}</a>
                        </div>

                        <div style="margin-top: 15px;">
                            <router-link :to="'/projects/' + $route.params.projectid + '/users'" class="link-style button button-default">
                                {{ i18n.add_remove_people }}
                            </router-link>
                        </div>
                    </div>
                </div>
                <component-actions>
                    <router-link :to="'/projects/' + $route.params.projectid + '/edit'"
                                    tag="li" class="action-item" 
                                    v-if="isShowEdit">
                        <i class="fa fa-edit" aria-hidden="true"></i>
                        {{i18n.edit_info}}
                    </router-link>

                    <router-link :to="'/projects/' + $route.params.projectid + '/status'"
                                    tag="li" class="action-item" 
                                    v-if="isShowEdit">
                        <i class="fa fa-trash" aria-hidden="true"></i>
                        {{i18n.delete}}
                    </router-link>

                    <router-link :to="'/projects/' + $route.params.projectid + '/reports'"
                                    tag="li" class="action-item">
                        <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                        {{i18n.activities}}
                    </router-link>
                </component-actions>
            </div>
            <div class="row">
                <div class="col-1"></div>
                <div class="col-5">
                    <div class="summary-card">
                        <router-link :to="'/projects/' + $route.params.projectid + '/todolists'" tag="h3" class="link-style">
                            <a>{{ i18n.todos }}</a>
                        </router-link>
                        <hr>
                        <div style="" class="text-left">
                            <div v-for="list in listSummary">
                                <h4>{{list.list_title}}</h4>
                                <ul>
                                    <li v-for="todo in list.todos">
                                        <span class="checkbox-style ellipsis-90"></span>{{todo.todo}}
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div v-if="listSummary.length < 1">
                            <div style="margin-top:12%">
                                <span class="summary-icon">
                                    <i class="fa fa-check fa-3x"></i>
                                </span>
                            </div>
                        </div>
                        <span class="preview-fade"></span>
                    </div>
                </div>
                <div class="col-5">
                    <div class="summary-card">
                        <router-link :to="'/projects/' + $route.params.projectid + '/messages'" tag="h3" class="link-style">
                            <a>{{ i18n.message_board }}</a>
                        </router-link>
                        <hr>
                        <div style="position: absolute;margin-top:10px;" class="text-left">
                            <div v-for="messageObj in messages" class="messages">
                                <div class="message-list">
                                    <img class="inbox-user-img" 
                                    :src="messageObj.avatar_url" width="20">
                                    <span class="inline-block">
                                        {{messageObj.message_title | truncate('38')}}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div v-if="messages.length < 1">
                            <div style="margin-top:12%">
                                <!-- <img :src="assetsDistPath+messageIcon" width="80" height="80" alt=""> -->
                                <span class="summary-icon">
                                    <i class="fa fa-envelope fa-3x"></i>
                                </span>
                            </div>
                        </div>
                        <span class="preview-fade"></span>
                    </div>
                </div>
            </div>
            <!-- <activities :i18n="i18n"></activities> -->
        </div>
    </div>
</template>

<script>
    import store from '../store';
    import ComponentActions from './partials/ComponentActions.vue';
    import Activities from './Activities.vue';
    export default {
        components: {
            Activities,
            ComponentActions
        },
        data() {
            return {
                i18n: {},
                listSummary : [],
                messages: [],
                users: [],
                project: ''
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
        computed: {
            isShowEdit: function() {
                var vm = this;
                return (vm.currentUser.roles.includes('administrator')) ||
                        (vm.currentUser.data.ID === vm.project.userID);
            }
        },
        methods: {
            fetchTodoSummary() {
                var vm = this;
                // vm.loading = true;

                var data = {
                    action: 'fpm-get-lists',
                    project_id: vm.$route.params.projectid,
                    nonce: fpm.nonce,
                    limit: 3
                };

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    vm.loading = false;
                    if ( resp.success ) {
                        vm.listSummary = resp.data;
                    }
                });
            },

            fetchMessageSummary() {
                var vm = this;
                // vm.loading = true;

                var data = {
                    action: 'fpm-get-messages',
                    project_id: vm.$route.params.projectid,
                    nonce: fpm.nonce,
                    limit: 5
                };

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    vm.loading = false;
                    if ( resp.success ) {
                        vm.messages = resp.data;
                    }
                });
            },

            fetchProject() {
                var vm = this;
                // vm.loading = true;

                var data = {
                    action: 'fpm-get-project',
                    project_id: vm.$route.params.projectid,
                    nonce: fpm.nonce
                };
                
                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    vm.loading = false;
                    
                    if ( resp.success ) {
                        vm.project = resp.data[0];
                    } else {
                        vm.$router.push({
                            path: `/?type=project&info=notfound`
                        });
                    }
                });
                
            }
        },

        created() {
            var vm = this,
                projectid,
                prevID,
                prevKey;

            var self = this;
            store.setLocalization( 'fpm-get-summary-local-data' ).then( function( data ) {
                self.i18n = data;
            });
            vm.loading = true;
            vm.fetchProject();
            vm.fetchTodoSummary();
            vm.fetchMessageSummary();
            vm.currentUser = fpm.currentUserInfo;

            prevID = localStorage.getItem('pid');
            projectid = vm.$route.params.projectid;

            if ( prevID !== projectid ) {
                prevKey = prevID + '-users';
                localStorage.removeItem(prevKey);
                localStorage.setItem('pid', projectid);

                store.fetchUsers( projectid ).then(function(resp){
                    vm.loading = false;
                    vm.users = resp.data;
                    var key = projectid + '-users';
                    localStorage.setItem(key, JSON.stringify(vm.users));
                });
            } else {
                var key = projectid + '-users';
                vm.users = JSON.parse(localStorage.getItem(key));
                if (!vm.users) {
                    store.fetchUsers( projectid ).then(function(resp){
                        vm.loading = false;
                        vm.users = resp.data;
                        localStorage.setItem(key, JSON.stringify(vm.users));
                    });
                }
            }
        }
    }
</script>

<style>
    .p-r-10 {
        padding-right: 10px;
    }
    .p-l-10 {
        padding-left: 10px;
    }
    .checkbox-style {
        padding: 0px 9px;
        margin-right: 10px;
        margin-left: 10px;
        border: 1px solid #ccc;
        border-radius: 3px;
    }
    .link-style a:link {
        text-decoration: none;
        cursor: pointer;
    }

    .link-style a:visited {
        text-decoration: none;
        cursor: pointer;
    }

    .link-style a:hover {
        text-decoration: underline;
        cursor: pointer;
    }

    .link-style a:active {
        text-decoration: underline;
        cursor: pointer;
    }
    .small-round-image {
        border-radius: 40%;
    }
    .text-center {
        text-align: center;
    }
    .text-left {
        text-align: left;
    }
    .text-right {
        text-align: right;
    }

    .summary-section {
        background: #ffffff;
        padding-bottom: 40px;
        /*border-radius: 5px;*/
        border: 1px solid #e5e5e5;
        box-shadow: 0 1px 1px rgba(0,0,0,.04);
        overflow: hidden;
    }
    span.summary-icon i {
        padding: 9px 12px 12px;
        border-radius: 50px;
        border: 1px solid #267cb5;
        color: white;
        background: #267cb5;
    }
    .summary-card {
        position: relative;
        padding: 10px 25px;
        border-radius: 5px;
        text-align: center;
        position: relative;
        border: 1px solid #e5e5e5;
        -webkit-box-shadow: 0 1px 1px rgba(0,0,0,0.04);
        box-shadow: 0 1px 1px rgba(0,0,0,0.04);
        background: #fff;
        height: 200px;
        overflow: hidden;
    }

    .summary-card ul li{
        margin-bottom: 10px;
    }

    .summary-card h3,
    .summary-card h4 {
        margin: 15px 0px;
        padding: 0px;
    }

    .users-summary {
        padding: 0.7em 2em 1em;
        border-radius: 3px;
        text-align: center;
        height: auto;
    }
    .project-info {
        position: relative;
        padding: 30px 40px 10px;
    }

    .messages .message-list {
        overflow: hidden;
        margin-bottom: 10px;
    }

    .show-edit {
        padding-top: 7px;
        padding-right: 7px;
        position: absolute;
        right: 0;
        top: 0;
        cursor: pointer;
    }
    .project-settings .fa {
        color: #b5b5b5;
    }
    .inbox-user-img {
        display: inline-block;
        vertical-align: top;
        border-radius: 45px;
        margin-right: 5px;
    }
</style>