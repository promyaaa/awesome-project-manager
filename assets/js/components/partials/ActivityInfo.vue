<template>
    <div>
        <img :src="activity.avatar_url" alt="" class="small-round-image activity-avatar" style="margin-right:15px; margin-top: 0px;">

        <div v-if="isCreateTodo" class="activity-info-block">
            <strong>{{activity.user_name}}</strong> created a <strong>Todo</strong>
            <span class="checkbox-style"></span>
            <router-link :to="'/projects/' + activity.projectID + '/todolists/' + activity.parentID + '/todos/' + activity.activity_id" tag="span">
                <a>{{activity.activity}}</a>
            </router-link>
            at {{ activity.formatted_time }}
        </div>

        <!-- <div v-if="isUpdateTodo">
            <strong>{{activity.user_name}}</strong> updated a <strong>Todo</strong> <br>
            <div class="m-t-5" style="cursor:pointer">
                <span class="checkbox-style"></span>
                <router-link :to="'/projects/' + activity.projectID + '/todolists/' + activity.parentID + '/todos/' + activity.activity_id" tag="span">
                    <a>{{activity.activity | truncate('28')}}</a>
                </router-link>
            </div>
        </div> -->

        <div v-if="isCheckTodo" class="activity-info-block">
            <strong class="mr-5">{{activity.user_name}}</strong> checked off a <strong class="ml-5">Todo</strong>
            <span class="checkbox-checked-style"><i class="fa fa-check" aria-hidden="true"></i></span>
            <router-link :to="'/projects/' + activity.projectID + '/todolists/' + activity.parentID + '/todos/' + activity.activity_id" tag="span">
                <a>{{activity.activity}}</a>
            </router-link>
            at {{ activity.formatted_time }}
        </div>

        <div v-if="isUncheckTodo" class="activity-info-block">
            <strong>{{activity.user_name}}</strong> re-opened a <strong>Todo</strong>
            <span class="checkbox-style"></span>
            <router-link :to="'/projects/' + activity.projectID + '/todolists/' + activity.parentID + '/todos/' + activity.activity_id" tag="span">
                <a>{{activity.activity}}</a>
            </router-link>
            at {{ activity.formatted_time }}
        </div>

        <div v-if="isDeleteTodo" class="activity-info-block">
            <strong>{{activity.user_name}}</strong> deleted a <strong>Todo</strong>
            <i style="color: #D54E21;">"{{activity.activity}}"</i>
            at {{ activity.formatted_time }}
        </div>

        <div v-if="isCreateMessage" class="activity-info-block">
            <strong>{{activity.user_name}}</strong> added a new <strong>Message</strong> called 
            <router-link :to="'/projects/' + activity.projectID + '/messages/' + activity.activity_id" tag="span">
                <a>{{activity.activity}}</a>
            </router-link>
            at {{ activity.formatted_time }}
        </div>

        <!-- <div v-if="isUpdateMessage">
            <strong>{{activity.user_name}}</strong> updated a new <strong>Message</strong> called
            <div class="m-t-5">
                <router-link :to="'/projects/' + activity.projectID + '/messages/' + activity.activity_id" tag="span">
                    <a>{{activity.activity | truncate('28')}}</a>
                </router-link>  
            </div>
        </div> -->

        <div v-if="isDeleteMessage" class="activity-info-block">
            <strong>{{activity.user_name}}</strong> deleted a <strong>Message</strong> called
            <i style="color: #D54E21">"{{activity.activity}}"</i>
            at {{ activity.formatted_time }}
        </div>
    </div>
</template>

<style>
    .m-t-5 {
        margin-top: 5px;
    }
    .checkbox-checked-style {
        padding: 0px 2px;
        margin-right: 10px;
        margin-left: 10px;
        border: 1px solid #ccc;
        /*border-radius: 3px*/
    }
    .activity-time {
        font-style: italic;
        margin-left: 5px;
        color: #72777c;
    }
    .activity-info-block {
        /*display: flex; */
        padding-bottom: 15px; 
        padding-left: 10px;
        text-align: left;
    }
</style>

<script>
    export default {
        props: ['activity', 'i18n'],
        filters: {
            truncate: function(string, value) {
                var dotdot = '';

                if (string.length > value) {
                    dotdot = '...';
                }
                return string.substring(0, value) + dotdot;
            }
        },
        computed: {
            isCreateTodo() {
                return this.activity.activity_type === 'create_todo';
            },
            isCheckTodo() {
                return this.activity.activity_type === 'check_todo';
            },
            isUncheckTodo() {
                return this.activity.activity_type === 'uncheck_todo';
            },
            isUpdateTodo() {
                return this.activity.activity_type === 'update_todo';
            },
            isDeleteTodo() {
                return this.activity.activity_type === 'delete_todo';
            },
            isCreateMessage() {
                return this.activity.activity_type === 'create_message';
            },
            isUpdateMessage() {
                return this.activity.activity_type === 'update_message';
            },
            isDeleteMessage() {
                return this.activity.activity_type === 'delete_message';
            }
        },
    }
</script>