<template>
    <div class="container lists">
        <div class="row">
            <div class="col-2"></div>
            <div class="col-8">
                <div class="text-center assignment-heading">
                    <h1>{{ i18n.my_assignments }}</h1>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <div class="row" v-for="todo in todos" style="padding-left:20px; padding-bottom:5px">
                    <div class="col-2 text-right">
                        <span class="checkbox-checked-style" v-if="+todo.is_complete">
                            <i class="fa fa-check" aria-hidden="true"></i>
                        </span>
                        <span class="checkbox-style" v-else></span>
                    </div>
                    <div class="col-10" style="margin-left:0px">
                        <router-link :to="'/projects/' + todo.projectID + '/todolists/' + todo.listID + '/todos/' +todo.ID" tag="div" class="my-todo">
                            <a>{{todo.todo}}</a>, <span><i style="font-size:12px">Created at {{todo.formatted_created}}</i></span>,
                            <span v-bind:class="[todo.is_overdue ? 'overdue' : 'due']">
                                <span v-if="todo.formatted_due_date">Due on</span>
                                {{todo.formatted_due_date}}
                            </span>
                        </router-link>
                        <!-- <div v-if="todo.formatted_due_date" style="padding-top:12px">
                            <i class="fa fa-calendar p-r-5" aria-hidden="true" style="color: #b5b5b5"></i> 
                            <span v-bind:class="[todo.is_overdue ? 'overdue' : 'due']">Due on {{todo.formatted_due_date}}</span>
                        </div> -->
                    </div>
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
    .my-todo {
        margin-top: -3px;
        text-decoration: none;
        color: #ccc;
    }

    .my-todo a{
        text-decoration: none;
        color: #444;
    }
</style>

<script>
    import store from '../store';
    export default {
        data() {
            return {
                i18n: {},
                todos:[]
            }
        },
        methods: {
            fetchAssignments() {
                var vm = this;
                var data = {
                    action: 'fpm-get-todos',
                    nonce: fpm.nonce,
                };

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    if ( resp.success ) {
                        vm.todos = resp.data;
                    }
                });
            }
        },

        created() {
            var self = this;
            store.setLocalization( 'fpm-get-myassignment-local-data' ).then( function( data ) {
                self.i18n = data;
            });

            this.fetchAssignments();
        }
    }
</script>