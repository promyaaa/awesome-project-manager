<template>
    <div>
        <img :src="activity.avatar_url" alt="" class="small-round-image activity-avatar" style="margin-right:15px; margin-top: 0px;">

        <div v-if="isCreateTodo" class="text-left" style="padding-top:2px;">
            <div class="ellipsis-90">
                <strong>{{activity.user_name}}</strong> {{ i18n.create_a_todo }} ,
                <span class="checkbox-style"></span>
                <router-link :to="'/projects/' + activity.projectID + '/todolists/' + activity.listID + '/todos/' + activity.activity_id" tag="span">
                    <a>{{activity.activity}}</a>
                </router-link>
            </div>
        </div>

        <div v-if="isUpdateTodo" class="text-left" style="padding-top:2px;">
            <div class="ellipsis-90">
                <strong>{{activity.user_name}}</strong> {{ i18n.updated_a_todo }} ,
                <span class="checkbox-style"></span>
                <router-link :to="'/projects/' + activity.projectID + '/todolists/' + activity.listID + '/todos/' + activity.activity_id" tag="span">
                    <a>{{activity.activity}}</a>
                </router-link>
            </div>
        </div>

        <div v-if="isDeleteTodo" class="text-left" style="padding-top:2px;">
            <div class="ellipsis-90">
                <strong>{{activity.user_name}}</strong> {{ i18n.delete_a_todo }} ,
                <span class="checkbox-style"></span>
                <span>{{activity.activity}}</span>
            </div>
        </div>

        <div v-if="isCheckTodo" class="text-left" style="padding-top:2px;">
            <div class="ellipsis-90">
                <strong>{{activity.user_name}}</strong> {{ i18n.checked_off_todo }} ,
                <span class="checkbox-checked-style">
                    <i class="fa fa-check" aria-hidden="true"></i>
                </span>
                <router-link :to="'/projects/' + activity.projectID + '/todolists/' + activity.listID + '/todos/' + activity.activity_id" tag="span">
                    <a>{{activity.activity}}</a>
                </router-link>
            </div>
        </div>

        <div v-if="isUncheckTodo" class="text-left" style="padding-top:2px;">
            <div class="ellipsis-90">
                <strong>{{activity.user_name}}</strong> {{ i18n.reopen_a_todo }} ,
                <span class="checkbox-style"></span>
                <router-link :to="'/projects/' + activity.projectID + '/todolists/' + activity.listID + '/todos/' + activity.activity_id" tag="span">
                    <a>{{activity.activity}}</a>
                </router-link>
            </div>
        </div>

        <div v-if="isCreateMessage" class="text-left" style="padding-top:2px;">
            <div class="ellipsis-90">
                <strong>{{activity.user_name}}</strong> {{ i18n.created_a_message_called }}
                <router-link :to="'/projects/' + activity.projectID + '/messages/' + activity.activity_id" tag="span">
                    <a>{{activity.activity}}</a>
                </router-link>
            </div>
        </div>

        <div v-if="isUpdateMessage" class="text-left" style="padding-top:2px;">
            <div class="ellipsis-90">
                <strong>{{activity.user_name}}</strong> {{ i18n.udpated_a_message_called }}
                <router-link :to="'/projects/' + activity.projectID + '/messages/' + activity.activity_id" tag="span">
                    <a>{{activity.activity}}</a>
                </router-link>
            </div>
        </div>

        <div v-if="isDeleteMessage" class="text-left" style="padding-top:2px;">
            <div class="ellipsis-90">
                <strong>{{activity.user_name}}</strong> {{ i18n.deleted_a_message_called }}
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
        margin-left: 10px;
        border: 1px solid #ccc;
    }
</style>

<script>
    export default {
        props: ['activity', 'i18n'],

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