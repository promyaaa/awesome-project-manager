<template>
    <div class="container lists">
        <div class="row">
            <div class="col-2"></div>
            <div class="col-8">
                <div class="text-center assignment-heading">
                    <h1>My Assignments</h1>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div v-for="todo in todos" style="padding-left:20px; padding-bottom:20px">
                    <span class="checkbox-checked-style" v-if="+todo.is_complete">
                        <i class="fa fa-check" aria-hidden="true"></i>
                    </span>
                    <span class="checkbox-style" v-else></span>
                    <router-link :to="'/projects/' + todo.projectID + '/todolists/' + todo.listID + '/todos/' + todo.ID" tag="span">
                        {{todo.todo}}
                    </router-link>
                </div>
            </div>
        </div>
        
    </div>
</template>

<style>
    .assignment-heading {
        border-bottom: 2px solid #eee;
        margin-bottom:20px;
        padding-bottom:20px;
    }
</style>

<script>
    export default {
        data() {
            return {
                todos:[]
            }
        },
        methods: {
            fetchAssignments() {
                var vm = this;
                // vm.loading = true;
                var data = {
                    action: 'fpm-get-todos',
                    nonce: fpm.nonce,
                };

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    // vm.loading = false;
                    console.log(resp);
                    if ( resp.success ) {
                        vm.todos = resp.data;
                    }
                });
            }
        },

        created() {
            this.fetchAssignments();
        }
    }
</script>