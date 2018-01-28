<template>
    <div>
        <img :src="activity.avatar_url" alt="" class="small-round-image activity-avatar" style="margin-right:15px; margin-top: 0px;">

        <div v-if="isCreateTodo">
            <strong>{{activity.user_name}}</strong> created a <strong>Todo</strong> <br>
            <div class="m-t-5">
                <span class="checkbox-style"></span>
                <router-link :to="'/projects/' + activity.projectID + '/todolists/' + activity.listID + '/todos/' + activity.activity_id" tag="span">
                    <a>{{activity.activity | truncate('25')}}</a>
                </router-link>  
            </div>
        </div>

        <div v-if="isUpdateTodo">
            <strong>{{activity.user_name}}</strong> updated a <strong>Todo</strong> <br>
            <div class="m-t-5" style="cursor:pointer">
                <span class="checkbox-style"></span>
                <router-link :to="'/projects/' + activity.projectID + '/todolists/' + activity.listID + '/todos/' + activity.activity_id" tag="span">
                    <a>{{activity.activity | truncate('25')}}</a>
                </router-link>
            </div>
        </div>

        <div v-if="isCheckTodo">
            <strong>{{activity.user_name}}</strong> checked off a <strong>Todo</strong> <br>
            <div class="m-t-5" style="cursor:pointer">
                <span class="checkbox-checked-style"><i class="fa fa-check" aria-hidden="true"></i></span>
                <router-link :to="'/projects/' + activity.projectID + '/todolists/' + activity.listID + '/todos/' + activity.activity_id" tag="span">
                    <a>{{activity.activity | truncate('25')}}</a>
                </router-link>
            </div>
        </div>

        <div v-if="isUncheckTodo">
            <strong>{{activity.user_name}}</strong> re-open a <strong>Todo</strong> <br>
            <div class="m-t-5" style="cursor:pointer">
                <span class="checkbox-style"></span>
                <router-link :to="'/projects/' + activity.projectID + '/todolists/' + activity.listID + '/todos/' + activity.activity_id" tag="span">
                    <a>{{activity.activity | truncate('25')}}</a>
                </router-link>
            </div>
        </div>

        <div v-if="isDeleteTodo">
            <strong>{{activity.user_name}}</strong> deleted a <strong>Todo</strong> <br>
            <div class="m-t-5" style="cursor:default">
                <span class="checkbox-style"></span>
                <span style="color:a2a2a2;">{{activity.activity | truncate('25')}}</span>
            </div>
        </div>

        <div v-if="isCreateMessage">
            <strong>{{activity.user_name}}</strong> added a new <strong>Message</strong> called
            <div class="m-t-5">
                <router-link :to="'/projects/' + activity.projectID + '/messages/' + activity.activity_id" tag="span">
                    <a>{{activity.activity | truncate('25')}}</a>
                </router-link>  
            </div>
        </div>

        <div v-if="isUpdateMessage">
            <strong>{{activity.user_name}}</strong> updated a new <strong>Message</strong> called
            <div class="m-t-5">
                <router-link :to="'/projects/' + activity.projectID + '/messages/' + activity.activity_id" tag="span">
                    <a>{{activity.activity | truncate('25')}}</a>
                </router-link>  
            </div>
        </div>

        <div v-if="isDeleteMessage">
            <strong>{{activity.user_name}}</strong> deleted a <strong>Message</strong> called<br>
            <div class="m-t-5" style="cursor:default">
                <span style="color:a2a2a2;">{{activity.activity | truncate('25')}}</span>
            </div>
        </div>
    </div>
</template>

<style>
    .m-t-5 {
        margin-top: 5px;
    }
    .checkbox-checked-style {
        padding: 0px 1px;
        margin-right: 10px;
        margin-left: 10px;
        border: 1px solid #ccc;
    }
</style>

<script>
    export default {
        props: ['activity', 'i18n'],
        filters: {
            truncate: function(string, value) {
                return string.substring(0, value) + '...';
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