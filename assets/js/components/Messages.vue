<template>
    <div>
        <div class="container">
            <!-- <div class="row">
                <div class="col-1"></div>
                <div class="col-10 section-head" v-if="project">
                    <router-link :to="'/projects/' + $route.params.projectid" class="link-style" tag="span">
                        <a>{{project.project_title}}</a>
                    </router-link>
                </div>
            </div> -->
            <div class="row">
                <div class="col-12">
                    <div class="row">
                        <div class="col-1"></div>
                        <div class="col-10">
                            <div v-if="project" class="project-navigation">
                                <router-link :to="'/projects/' + $route.params.projectid" class="link-style" tag="h3">
                                    <a>{{project.project_title}}</a>
                                </router-link>
                            </div>
                        </div>
                    </div>
                    <div class="messages-section">
                        <div class="row">
                            <div class="col-4">
                                <router-link :to="'/projects/' + $route.params.projectid + '/messages/new'" class="button button-default">
                                    +Add
                                </router-link>
                            </div>
                            <div class="col-4 text-center" style="border-bottom: 2px solid grey;">
                                <h3>Message Board</h3>
                            </div>
                        </div>

                        <div class="loading" v-if="loading">
                            <p>Loading . . .</p>
                        </div>

                        <div v-if="messages.length < 1 && !loading">
                            <h4>No Message Added Yet</h4>
                        </div>
                        <!-- <pre>
                            {{messages}}
                        </pre> -->
                        <div v-if="messages.length > 0 && !loading">
                            <ul>
                                <li v-for="(messageObj, mindex) in messages" style="border-bottom: 1px solid #f2f2f2">
                                    <div class="row">
                                        <div class="col-2 text-center">
                                            <img :src="messageObj.avatar_url" class="small-round-image" style="margin-top: 10px">
                                        </div>
                                        <div class="col-10">
                                            <div>
                                                <router-link :to="'/projects/' + $route.params.projectid + '/messages/' + messageObj.ID" tag="h3" class="ellipsis-90 link-style">
                                                    <a>{{messageObj.message_title}}</a>
                                                </router-link>
                                                <p>posted by <strong>{{messageObj.user_name}}</strong> , <span>{{messageObj.formatted_created}}</span></p> 
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>   
                        </div>
                        <br>
                        <div class="row" v-if="messages.length < messageCount">
                            <div class="col-12 text-center">
                                <button class="button button-default" @click="loadMoreMessages">Load More...{{messageCount}}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
    .messages-section {
        background-color: #fff;
        padding: 15px;
        border-radius: 5px;
    }
    .project-navigation {
        text-align: center;
    }
    /*.message-title{
        cursor: pointer;
        white-space: nowrap; 
        width: 90%;
        overflow: hidden;
        text-overflow: ellipsis;
    }*/
</style>

<script>
    export default {
        components: {
            
        },

        data() {
            return {
                messages: [],
                loading: false,
                mindex: 0,
                // isShowMessageForm: false,
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
                // vm.loading = true;
                
                var data = {
                    action: 'fpm-get-project',
                    project_id: vm.$route.params.projectid,
                    nonce: fpm.nonce,
                };

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    console.log(resp);
                    if ( resp.success ) {
                        vm.project = resp.data[0];
                        vm.messageCount = vm.project.message_count;
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
                    console.log(resp);
                    if ( resp.success ) {
                        // vm.projectTitle = resp.data[0].project_title;
                        for(var i = 0; i < resp.data.length; i++ ) {
                            vm.messages.push(resp.data[i]);
                        }
                    }
                });
            },

            // toggleMessageForm: function() {
            //     var vm = this;
            //     vm.messageTitle = '';
            //     vm.message = '';
            //     vm.isShowMessageForm = !vm.isShowMessageForm;
            // },

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
                        console.log(resp);
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
            vm.fetchProjectInfo();
            vm.fetchMessages();
        },

        mounted() {

        }
    }
</script>