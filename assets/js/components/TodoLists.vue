<template>
    <div class="container">
        <project-nav v-on:get-project="setProject"></project-nav>
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

        <div class="lists border-for-nav">
            <div class="row">
                <div class="col-4">
                    <button class="button button-default" @click.prevent="toggleListForm" v-if="!isShowListForm">{{ i18n.make_list_btn }}</button>
                </div>
                <div class="col-4 text-center" style="border-bottom: 2px solid grey;margin-bottom:35px;">
                    <h3>{{ i18n.todos }}</h3>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div v-if="isShowListForm" class="add_form_style">
                        <div>
                            <input type="text"
                                name="list_title"
                                v-model="listTitle"
                                class="form-control"
                                :placeholder="i18n.name_list_placeholder"
                                @keyup.enter="createList"
                                @keyup.esc="toggleListForm"
                                v-focus>
                        </div>
                        <div class="action">
                            <button class="button button-primary"
                                    @click.prevent="createList"
                                    >{{ i18n.create_list }}</button>
                            <button class="button button-default" @click="toggleListForm">{{ i18n.cancel }}</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="text-center" v-if="loading">
                        <i class="fa fa-refresh fa-spin fa-3x fa-fw" aria-hidden="true"></i>
                        <p>{{ i18n.loading }}</p>
                    </div>

                    <div v-if="lists.length < 1 && !loading">
                        <h4>{{ i18n.no_list_added_yet }}</h4>
                    </div>

                    <div v-if="lists.length > 0 && !loading">
                        <ul>
                            <list :i18n="i18n" v-for="(list, sindex) in lists" :list="list" :sindex="sindex" :key="list.ID"></list>
                        </ul>
                        <div class="row" v-if="lists.length < listCount">
                            <div class="col-12 text-center">
                                <button class="button button-default" @click="loadMoreLists">{{ i18n.load_more_btn }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
    .box {
        position: relative;
        min-width: 255px;
        border: 1px solid #e5e5e5;
        -webkit-box-shadow: 0 1px 1px rgba(0,0,0,0.04);
        box-shadow: 0 1px 1px rgba(0,0,0,0.04);
        background: #fff;
        padding: 15px 20px;
        border-radius: 5px;
    }
    .add_form_style {
        padding: 20px;
        border: 1px dotted #ccc;
        /*border-radius: 5px;*/
        background: #f3f4f5;
    }


</style>

<script>
    import store from '../store';
    import List from './partials/ListComponent.vue';
    import ProjectNav from './partials/ProjectNavComponent.vue';
    export default {
        components: {
            'list': List,
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
                i18n: {},
                lists: [],
                isShowListForm: false,
                listTitle: '',
                loading: false,
                currentUser: '',
                listCount: '',
                project: ''
            }
        },

        methods: {
            setProject( project ) {
                this.project = project;
            },

            toggleListForm: function() {
                var vm = this;
                vm.isShowListForm = !vm.isShowListForm;
            },

            fetchLists: function() {
                var vm = this;
                vm.loading = true;

                var data = {
                    action: 'fpm-get-lists',
                    project_id: vm.$route.params.projectid,
                    nonce: fpm.nonce,
                };

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    vm.loading = false;
                    if ( resp.success ) {
                        vm.lists = resp.data;
                    }
                });
            },

            loadMoreLists() {
                var vm = this;
                // vm.loadMore = true;
                var data = {
                    action: 'fpm-load-more-lists',
                    nonce: fpm.nonce,
                    project_id: vm.$route.params.projectid,
                    offset: vm.lists.length
                };

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    // vm.loadMore = false;
                    if ( resp.success ) {
                        for(var i = 0; i < resp.data.length; i++ ) {
                            vm.lists.push(resp.data[i]);
                        }
                    }
                });
            },

            fetchProjectInfo: function() {
                var vm = this;
                // vm.loading = true;

                var data = {
                    action: 'fpm-get-project',
                    project_id: vm.$route.params.projectid,
                    nonce: fpm.nonce,
                };

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    if ( resp.success ) {
                        vm.project = resp.data[0];
                        vm.listCount = vm.project.list_count;
                    } else {
                        vm.$router.push({
                            path: `/?type=project&info=notfound`
                        });
                    }
                });
            },

            createList: function() {
                var vm = this,
                list,
                data = {
                    action : 'fpm-create-list',
                    nonce : fpm.nonce,
                    title: vm.listTitle,
                    project_id: vm.$route.params.projectid,
                    user_name: vm.currentUser.data.display_name
                };

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    if ( resp.success ) {
                        resp.data.listInfo.list_title = vm.listTitle;
                        resp.data.listInfo.todos = [];
                        vm.lists.unshift(resp.data.listInfo);
                        vm.listTitle = '';
                    } else {
                        // vm.message = resp.data;
                    }
                });
            }
        },

        created() {
            var vm = this;

            store.setLocalization( 'fpm-get-todo-lists-local-data' ).then( function( data ) {
                vm.i18n = data;
            });

            vm.fetchProjectInfo();
            vm.fetchLists();
            vm.currentUser = fpm.currentUserInfo;

        }
    }
</script>