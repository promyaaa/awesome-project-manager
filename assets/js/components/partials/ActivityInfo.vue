<template>
    <div>
        <img :src="activity.avatar_url" alt="" class="small-round-image activity-avatar" style="margin-right:15px; margin-top: 0px;">

        <div v-if="isCreateTodo" class="text-left" style="padding-top:2px;">
            <div>
                <strong>{{activity.user_name}}</strong> created a <strong>Todo ,</strong>
                <span class="checkbox-style"></span>
                <router-link :to="'/projects/' + activity.projectID + '/todolists/' + activity.listID + '/todos/' + activity.activity_id" tag="span">
                    <a>{{activity.activity}}</a>
                </router-link> 
            </div>
        </div>

        <div v-if="isUpdateTodo" class="text-left" style="padding-top:2px;">
            <div>
                <strong>{{activity.user_name}}</strong> updated a <strong>Todo ,</strong>
                <span class="checkbox-style"></span>
                <router-link :to="'/projects/' + activity.projectID + '/todolists/' + activity.listID + '/todos/' + activity.activity_id" tag="span">
                    <a>{{activity.activity}}</a>
                </router-link> 
            </div>
        </div>

        <div v-if="isDeleteTodo" class="text-left" style="padding-top:2px;">
            <div>
                <strong>{{activity.user_name}}</strong> deleted a <strong>Todo ,</strong>
                <span class="checkbox-style"></span>
                <span>{{activity.activity}}</span>
            </div>
        </div>

        <div v-if="isCheckTodo" class="text-left" style="padding-top:2px;">
            <div>
                <strong>{{activity.user_name}}</strong> checked off a <strong>Todo ,</strong>
                <span class="checkbox-checked-style" style="margin-left: 10px;">
                    <i class="fa fa-check" aria-hidden="true"></i>
                </span>
                <router-link :to="'/projects/' + activity.projectID + '/todolists/' + activity.listID + '/todos/' + activity.activity_id" tag="span">
                    <a>{{activity.activity}}</a>
                </router-link> 
            </div>
        </div>

        <div v-if="isUncheckTodo" class="text-left" style="padding-top:2px;">
            <div>
                <strong>{{activity.user_name}}</strong> re-open a <strong>Todo ,</strong>
                <span class="checkbox-style"></span>
                <router-link :to="'/projects/' + activity.projectID + '/todolists/' + activity.listID + '/todos/' + activity.activity_id" tag="span">
                    <a>{{activity.activity}}</a>
                </router-link> 
            </div>
        </div>

        <div v-if="isCreateMessage" class="text-left" style="padding-top:2px;">
            <div>
                <strong>{{activity.user_name}}</strong> created a <strong>Message ,</strong> called
                <router-link :to="'/projects/' + activity.projectID + '/messages/' + activity.activity_id" tag="span">
                    <a>{{activity.activity}}</a>
                </router-link> 
            </div>
        </div>

        <div v-if="isUpdateMessage" class="text-left" style="padding-top:2px;">
            <div>
                <strong>{{activity.user_name}}</strong> updated a <strong>Message ,</strong> called
                <router-link :to="'/projects/' + activity.projectID + '/messages/' + activity.activity_id" tag="span">
                    <a>{{activity.activity}}</a>
                </router-link> 
            </div>
        </div>

        <div v-if="isDeleteMessage" class="text-left" style="padding-top:2px;">
            <div>
                <strong>{{activity.user_name}}</strong> deleted a <strong>Message ,</strong> called
                <span>{{activity.activity}}</span>
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
        border: 1px solid #ccc;
    }
</style>

<script>
    export default {
        props: ['activity'],
        data() {
            return {

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

        methods: {

        },

        created() {

        }
    }
</script>