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

        <div class="lists">
            <div class="row">
                <div class="col-4">
                    <router-link :to="'/projects/' + $route.params.projectid + '/messages/new'" class="button button-default">
                        +{{ i18n.add_new_msg_btn }}
                    </router-link>
                </div>
                <div class="col-4 text-center" style="border-bottom: 2px solid grey;margin-bottom:35px;">
                    <h3>{{ i18n.message_heading }}</h3>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="text-center" v-if="loading">
                        <i class="fa fa-refresh fa-spin fa-3x fa-fw" aria-hidden="true"></i>
                        <p>{{ i18n.loading }}</p>
                    </div>

                    <div v-if="messages.length < 1 && !loading">
                        <h4>{{ i18n.no_message_yet }}</h4>
                    </div>
                    <div v-if="messages.length > 0 && !loading">
                        <ul>
                            <li v-for="(messageObj, mindex) in messages" style="border-bottom: 1px solid #f2f2f2">
                                <div class="row">
                                    <div class="col-2 text-center">
                                        <img :src="messageObj.avatar_url" class="small-round-image" style="margin-top: 10px">
                                    </div>
                                    <div class="col-10">
                                        <div class="message-list-item">
                                            <router-link :to="'/projects/' + $route.params.projectid + '/messages/' + messageObj.ID" tag="h3" class="ellipsis-90 link-style">
                                                <a>{{messageObj.message_title}}</a>
                                            </router-link>
                                            <p>{{ i18n.posted_by}} <strong>{{messageObj.user_name}}</strong> , <span>{{messageObj.formatted_created}}</span></p>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <br>
                    <div class="row" v-if="messages.length < messageCount">
                        <div class="col-12 text-center">
                            <button class="button button-default" @click="loadMoreMessages">{{ i18n.load_more_btn }}{{messageCount}}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
    .message-list-item a {
        color: #444444;
    }
    .project-navigation {
        text-align: center;
    }

</style>

<script>
    import store from '../store';

    export default {
        components: {

        },

        data() {
            return {
                i18n: {},
                messages: [],
                loading: false,
                mindex: 0,
                message: '',
                project: {},
                messageTitle: '',
                messageCount: '',
                loadMore: false
            }
        },

        methods: {
            fetchProjectInfo: function() {
                var vm = this;

                var data = {
                    action: 'fpm-get-project',
                    project_id: vm.$route.params.projectid,
                    nonce: fpm.nonce,
                };

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    
                    if ( resp.success ) {
                        vm.project = resp.data[0];
                        vm.messageCount = vm.project.message_count;
                    } else {
                        vm.$router.push({
                            path: `/?type=project&info=notfound`
                        });
                    }
                });
            },

            loadMoreMessages() {
                var vm = this;
                // vm.loadMore = true;
                var data = {
                    action: 'fpm-load-more-messages',
                    nonce: fpm.nonce,
                    project_id: vm.$route.params.projectid,
                    offset: vm.messages.length
                };

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    // vm.loadMore = false;
                    if ( resp.success ) {
                        for(var i = 0; i < resp.data.length; i++ ) {
                            vm.messages.push(resp.data[i]);
                        }
                    }
                });
            },

            // fetchMessageCount: function() {
            //     var vm = this,
            //         data = {
            //         action: 'fpm-get-message-count',
            //         nonce: fpm.nonce,
            //     };

            //     jQuery.post( fpm.ajaxurl, data, function( resp ) {
            //         console.log(resp);

            //         if ( resp.success ) {
            //             vm.messageCount = resp.data;
            //         }
            //     });
            // },

            fetchMessages: function() {
                var vm = this;
                vm.loading = true;

                var data = {
                    action: 'fpm-get-messages',
                    project_id: vm.$route.params.projectid,
                    nonce: fpm.nonce,
                };

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    vm.loading = false;
                    
                    if ( resp.success ) {
                        for(var i = 0; i < resp.data.length; i++ ) {
                            vm.messages.push(resp.data[i]);
                        }
                    }
                });
            },

            createMessage: function() {
                var vm = this,
                message,
                data = {
                    action : 'fpm-create-message',
                    nonce : fpm.nonce,
                    message_title: vm.messageTitle,
                    message: vm.message,
                    project_id: vm.$route.params.projectid
                };

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    if ( resp.success ) {
                        // resp.data.messageInfo.message = vm.message;
                        vm.messages.push(resp.data.messageInfo);
                        vm.message = '';
                        vm.messageTitle = '';
                    } else {
                        // vm.message = resp.data;
                    }
                });
            }
        },

        created() {
            var vm = this;

            store.setLocalization( 'fpm-get-messages-local-data' ).then( function( data ) {
                vm.i18n = data;
            });

            vm.fetchProjectInfo();
            vm.fetchMessages();
        },

        mounted() {

        }
    }
</script>