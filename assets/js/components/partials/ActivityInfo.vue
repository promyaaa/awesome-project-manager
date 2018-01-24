<template>
    <div class="m-t-5">
        <!-- <pre>
            {{activity}}
        </pre> -->
        <img :src="activity.avatar_url" alt="" class="small-round-image activity-avatar">

        <div v-if="isCreateTodo" style="margin-top:5px">
            <strong>{{activity.user_name}}</strong> created a todo <br>
            <div class="m-t-5">
                <span class="checkbox-style"></span>
                <!-- <i class="fa fa-square-o fa-5" aria-hidden="true"></i> -->
                <router-link :to="'/projects/' + activity.projectID + '/todolists/' + activity.listID + '/todos/' + activity.activity_id" tag="span">
                    <a>{{activity.activity}}</a>
                </router-link>  
            </div>
        </div>

        <div v-if="isCreateMessage" style="margin-top:5px">
            <strong>{{activity.user_name}}</strong> added a new message called
            <div class="m-t-5">
                <router-link :to="'/projects/' + activity.projectID + '/messages/' + activity.activity_id" tag="span">
                    <a>{{activity.activity}}</a>
                </router-link>  
            </div>
        </div>

    </div>
</template>

<style>
    .m-t-5 {
        margin-top: 5px;
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
            isCreateMessage() {
                return this.activity.activity_type === 'create_message';
            },
        },

        methods: {

        },

        created() {

        }
    }
</script>