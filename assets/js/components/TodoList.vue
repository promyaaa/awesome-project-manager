<template>
    <div>
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <router-link :to="'/projects/' + $route.params.projectid + '/todolists'" class="link-style" tag="h3">
                        <a><i class="fa fa-long-arrow-left p-r-10" aria-hidden="true"></i>To-dos</a>
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
                sindex: 0
            }
        },

        methods: {
            fetchListDetails: function() {
                var self = this;
                self.loading = true;
                
                var data = {
                    action: 'fpm-get-list-details',
                    project_id: self.$route.params.projectid,
                    list_id: self.$route.params.listid,
                    nonce: fpm.nonce,
                };

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    self.loading = false;
                    console.log(resp);
                    if ( resp.success ) {
                        self.list = resp.data[0];
                    }
                });
            }
        },

        created() {
            var self = this;
            self.fetchListDetails();

            console.log('Component created.')
        },

        mounted() {
            console.log('Component mounted.')
        }
    }
</script>