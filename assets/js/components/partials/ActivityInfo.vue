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
                <router-link :to="'/projects/' + activity.projectID + '/todolists/' + activity.listID + '/todos/' + activity.activity_id" tag="span">
                    <a>{{activity.activity}}</a>
                </router-link>  
            </div>
        </div>

        <div v-if="isUpdateTodo" style="margin-top:5px">
            <strong>{{activity.user_name}}</strong> updated a todo <br>
            <div class="m-t-5" style="cursor:pointer">
                <span class="checkbox-style"></span>
                <router-link :to="'/projects/' + activity.projectID + '/todolists/' + activity.listID + '/todos/' + activity.activity_id" tag="span">
                    <a>{{activity.activity}}</a>
                </router-link>
            </div>
        </div>

        <div v-if="isDeleteTodo" style="margin-top:5px">
            <strong>{{activity.user_name}}</strong> deleted a todo <br>
            <div class="m-t-5" style="cursor:default">
                <span class="checkbox-style"></span>
                <span style="color:a2a2a2;">{{activity.activity}}</span>
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
            isUpdateTodo() {
                return this.activity.activity_type === 'update_todo';
            },
            isCreateTodo() {
                return this.activity.activity_type === 'create_todo';
            },
            isDeleteTodo() {
                return this.activity.activity_type === 'delete_todo';
            }
        },

        methods: {

        },

        created() {

        }
    }
</script>