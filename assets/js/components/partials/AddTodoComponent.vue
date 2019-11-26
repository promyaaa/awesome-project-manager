<template>
    <div>
        <div class="row">
            <div class="col-12">
                <a href="#"
                    @click.prevent="showTodoForm(sindex, list)"
                    v-if="sectionIndex !== sindex"
                    style="margin-left: 63px;">+ {{ i18n.add_new_todo }}</a>
            </div>
        </div>
        
        <div class="row">
            <div class="col-1"></div>
            <div class="col-10">
                <div class="add_form_style" v-if="sectionIndex === sindex">

                        <div class="todo_name inline">
                            <input type="text"
                                v-model="todoName"
                                class="form-control"
                                :placeholder="i18n.add_todo_placeholder"
                                v-focus
                                required
                                @keyup.esc="hideTodoForm">
                        </div>
                        
                        <div>
                            <dropdown-autocomplete 
                                :currentselect="selected.display_name"
                                v-on:userselect="selectUser"></dropdown-autocomplete>
                        </div>
                        <div>
                            <date-picker id="add-duedate" v-model="todoDueDate"></date-picker>
                        </div>
                        <file-upload
                            :i18n="i18n"
                            v-on:attach="updateAttachments"
                            v-on:remove="removeAttachment"
                            :attachments="attachments"></file-upload>
                        <br>
                        
                        <div v-if="selected" style="margin-bottom: 20px;">
                            <input type="checkbox" id="checkbox" v-model="notifyAssignee">
                            notify assignee via email
                        </div>

                        <div class="inline">
                            <button class="button button-primary"
                                    @click.prevent="createTodo"
                                    :disabled="creatingTodo" 
                            >
                                <i v-if="creatingTodo" class="fa fa-refresh fa-spin mr-5"></i>
                                {{ i18n.add_todo }}
                            </button>
                            <button class="button button-default"
                                @click.prevent="hideTodoForm">
                                {{ i18n.cancel }}
                            </button>
                        </div>
                </div>
            </div>
        </div>

    </div>
</template>

<style>

</style>

<script>
    import store from '../../store';
    import FileUpload from './FileUploadComponent.vue';
    import DatePicker from './DatePickerComponent.vue';
    import DropdownAutocomplete from './DropdownAutocomplete.vue';
    export default {
        props: [ 'sindex', 'list', 'i18n' ],

        components: {
            FileUpload,
            DatePicker,
            DropdownAutocomplete,
        },

        data() {
            return {
                sectionIndex: -1,
                todoName: '',
                selected: '',
                todoDueDate: '',
                attachments: [],
                attachmentIDs: [],
                creatingTodo: false,
                notifyAssignee: false,
            }
        },

        directives: {
            focus: {
                inserted: function (el) {
                    el.focus()
                }
            }
        },

        methods: {
            selectUser(userObject) {
                this.selected = userObject;
            },

            updateAttachments: function(attachment) {
                var vm = this;

                vm.attachments.push(attachment);
                vm.attachmentIDs.push(attachment.id);
            },

            removeAttachment: function(index) {
                this.attachments.splice(index, 1);
                this.attachmentIDs.splice(index, 1);
            },

            showTodoForm: function( index ) {
                this.sectionIndex = index;
            },

            hideTodoForm: function() {
                this.sectionIndex = -1;
                this.todoName = '';
            },

            createTodo: function() {

                var vm = this,
                    todo,
                    data = {
                        action : 'fpm-insert-todo',
                        nonce : fpm.nonce,
                        todo: vm.todoName,
                        list_id: vm.list.ID,
                        project_id: vm.$route.params.projectid,
                        user_id: vm.currentUser.data.ID,
                        user_name: vm.currentUser.data.display_name,
                        assignee_id: vm.selected.ID,
                        assignee_name: vm.selected.display_name,
                        attachments: vm.attachmentIDs,
                        due_date: vm.todoDueDate
                    };

                if ( vm.notifyAssignee ) {
                    data.assignee_email = vm.selected.email;
                }

                if ( !vm.todoName ) {
                    return;
                }
                vm.creatingTodo = true;

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    if ( resp.success ) {
                        vm.creatingTodo = false;
                        todo = resp.data.todo;
                        todo.todo = vm.todoName;
                        todo.is_complete = '0';
                        todo.userID = vm.currentUser.data.ID;
                        todo.user_name = vm.currentUser.data.display_name;
                        todo.files = vm.attachments;
                        todo.listID = vm.list.ID;
                        todo.projectID = vm.$route.params.projectid;

                        if( vm.selected ) {
                            todo.assigneeID = vm.selected.ID;
                            todo.assignee_name = vm.selected.display_name;
                        } else {
                            todo.assigneeID = null;
                            todo.assignee_name = null;
                        }

                        vm.list.todos.unshift(todo);

                        vm.selected = '';
                        vm.todoName = '';
                        vm.attachments = [];
                        vm.attachmentIDs = [];

                        // var text = "A new todo ' " + todo.todo + " ' has been created to ' " + vm.list.list_title + " ' list item";
                        // var postUrl = 'https://hooks.slack.com/services/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';
                        // jQuery.ajax({
                        //     data: 'payload=' + JSON.stringify({
                        //         "text": text
                        //     }),
                        //     dataType: 'json',
                        //     processData: false,
                        //     type: 'POST',
                        //     url: postUrl
                        // });
                    } else {
                        vm.message = resp.data;
                    }
                });
            }

        },

        mounted() {
            // var self = this;
            // jQuery('#datepicker').datepicker({
            //     onSelect:function(selectedDate, datePicker) {
            //         self.date = selectedDate;
            //     }
            // });
        },

        created() {
            var vm = this,
                projectid,
                key;

            vm.currentUser = fpm.currentUserInfo;
        }
    }
</script>