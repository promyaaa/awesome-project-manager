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