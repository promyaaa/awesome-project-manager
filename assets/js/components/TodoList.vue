<template>
    <div>
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <router-link :to="'/projects/' + $route.params.projectid " class="link-style inline-block" tag="h3">
                        <a>{{project.project_title}}</a>
                    </router-link>
                    <router-link :to="'/projects/' + $route.params.projectid + '/todolists'" class="link-style inline-block" tag="h4">
                        <a><i class="fa fa-long-arrow-right p-l-10 p-r-10" aria-hidden="true"></i>To-dos</a>
                    </router-link>
                </div>
            </div>
            <div class="row">
            <!-- <pre>
                {{list}}
            </pre> -->
                <div class="col-12">
                    <div class="lists">
                    
                        <div class="loading" v-if="loading">
                            <p>Loading . . .</p>
                        </div>

                        <div v-if="list && !loading">
                            <ul>
                                <list is-single="true" :list="list" :sindex="0"></list>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
    .lists {
        background: #fff;
        padding: 15px 25px;
        border-radius: 5px;
    }
    .inline-block {
        display: inline-block;
    }
</style>

<script>
    import List from './partials/ListComponent.vue';
    import Comments from './partials/CommentsComponent.vue';
    export default {
        components: {
            'list': List,
            'comments': Comments
        },

        data() {
            return {
                list: {},
                loading: false,
                sindex: 0,
                project: ''
            }
        },

        methods: {
            fetchListDetails: function() {
                var vm = this;
                vm.loading = true;
                
                var data = {
                    action: 'fpm-get-list-details',
                    project_id: vm.$route.params.projectid,
                    list_id: vm.$route.params.listid,
                    nonce: fpm.nonce,
                };

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    vm.loading = false;
                    console.log(resp);
                    if ( resp.success ) {
                        vm.list = resp.data[0];
                        vm.project = resp.data[0].project_info;
                    }
                });
            }
        },

        created() {
            var vm = this;
            vm.fetchListDetails();
        },

        mounted() {
            
        }
    }
</script>