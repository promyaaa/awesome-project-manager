<template>
    <div>
        <div class="container">
            <div class="row text-center">
                <!-- {{project.project_title}} -->
                <div class="col-12">
                    <router-link :to="'/projects/' + $route.params.projectid" tag="h3">
                        <a>{{project.project_title}}</a>
                    </router-link>
                </div>
            </div>
            <!-- <div class="row">
                <div class="col-12">
                    <div class="list-title">
                        <h1 class="wp-heading-inline">TodoList</h1>
                        <a class="page-title-action" @click.prevent="toggleListForm" v-if="!isShowListForm">+ Add</a>
                    </div>
                    <div>
                        <div v-if="isShowListForm" class="add_form_style">
                            <div>
                                <input type="text" 
                                    name="list_title" 
                                    v-model="listTitle" 
                                    class="form-control" 
                                    placeholder="list title . . ." 
                                    @keyup.enter="createList" 
                                    @keyup.esc="toggleListForm"
                                    v-focus>
                            </div>
                            <div class="action">
                                <button class="button button-primary" 
                                        @click.prevent="createList"
                                        >Add</button>
                                <button class="button button-default" @click="toggleListForm">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->
            <div class="row">
                <div class="col-12">
                    <div class="lists">
                        <button class="button button-default" @click.prevent="toggleListForm" v-if="!isShowListForm">Make List</button>
                        <div style="margin: 40px 35px;">
                            <div v-if="isShowListForm" class="add_form_style">
                                <div>
                                    <input type="text" 
                                        name="list_title" 
                                        v-model="listTitle" 
                                        class="form-control" 
                                        placeholder="Name this list . . ." 
                                        @keyup.enter="createList" 
                                        @keyup.esc="toggleListForm"
                                        v-focus>
                                </div>
                                <div class="action">
                                    <button class="button button-primary" 
                                            @click.prevent="createList"
                                            >Add</button>
                                    <button class="button button-default" @click="toggleListForm">Cancel</button>
                                </div>
                            </div>
                        </div>
                        <div class="loading" v-if="loading">
                            <p>Loading . . .</p>
                        </div>

                        <div v-if="lists.length < 1 && !loading">
                            <h4>No Lists Added Yet</h4>
                        </div>
                        
                        <div v-if="lists.length > 0 && !loading">
                            <ul>
                                <list v-for="(list, sindex) in lists" :list="list" :sindex="sindex" :key="list.ID"></list>
                            </ul>
                            <div class="row" v-if="lists.length < listCount">
                                <div class="col-12 text-center">
                                    <button class="button button-default" @click="loadMoreLists">Load More...</button>
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
        border-radius: 5px;
    }
    

</style>

<script>
    import List from './partials/ListComponent.vue';
    export default {
        components: {
            'list': List
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
            toggleListForm: function() {
                var vm = this;
                vm.isShowListForm = !vm.isShowListForm;
                // vm.$nextTick(function () {
                //     vm.$refs.addList.focus();
                // });
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
                    console.log(resp);
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
                        // console.log(resp);
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

        mounted() {
            console.log('Component mounted.');
            // this.$on('deleted', function() {
            //     console.log('deleted');
            // });
        },

        created() {
            var vm = this;
            vm.fetchProjectInfo();
            vm.fetchLists();
            vm.currentUser = fpm.currentUserInfo;

        }
    }
</script>