<template>
    <div class="comment-content">
        <h3 style="padding-left: 14px;">{{ i18n.comment_label }}</h3>

        <div v-for="(commentObject, cindex) in comments" style="padding:0px 15px 15px 15px;border-radius: 5px;">
            <div v-if="editindex !== cindex">
                <img :src="commentObject.avatar_url" alt="">
                <div v-html="commentObject.comment"></div><br>
                {{ i18n.comment_by}} {{commentObject.user_name}}

                <br>
                <div class="action" style="border-bottom: 1px solid #eee; padding-bottom:10px;">
                    <span style="cursor: pointer;" @click="showCommentEditForm(commentObject, cindex)">
                        <a>{{i18n.edit}}</a> |
                    </span>
                    <span style="cursor: pointer;" @click="deleteComment(commentObject, cindex)">
                        <a>{{ i18n.delete }}</a>
                    </span>
                </div>
            </div>
            <!-- edit section -->
            <div v-if="editindex === cindex">
                <div class="add_form_style">
                    <vue-editor id="edit-comment" v-model="commentEditText" :editorToolbar="customToolbar"></vue-editor>
                    <br>
                    <button class="button button-primary"
                        @click.prevent="updateComment(commentObject)"
                        >{{ i18n.update }}</button>
                    <button class="button button-default" @click="cancelCommentEdit(cindex)">{{ i18n.cancel }}</button>
                </div>
            </div>
        </div>
        <br><br><hr>
        <div style="margin-top: 15px;">
            <div class="add_form_style">
                <vue-editor id="add-comment" v-model="comment" :editorToolbar="customToolbar"></vue-editor>
                <br>

                <div class="action">
                    <button class="button button-primary"
                        @click.prevent="addComment()"
                        >{{ i18n.add_comment }}</button>
                </div>
            </div>

        </div>

    </div>
</template>
<style>
    .comment-content {
        padding: 20px 32px;
    }
</style>
<script>
    import { VueEditor } from 'vue2-editor'
    export default {
        props: ['comments', 'type', 'i18n'],
        components: {
            VueEditor
        },
        data() {
            return {
                cloneObject: '',
                loading: false,
                comment: '',
                commentEditText: '',
                editindex: -1,
                customToolbar : [
                    ['bold', 'italic', 'underline', 'strike'],
                    ['blockquote', 'code-block'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'indent': '-1'}, { 'indent': '+1' }],
                    [{ 'header': [3, 4, 5, 6, false] }],
                    [{ 'align': [] }],
                ]
            }
        },

        methods: {
            addComment: function() {
                var vm = this,
                    data;
                if (!vm.comment.trim()) {
                    return;
                }
                    data = {
                        action : 'fpm-insert-comment',
                        nonce : fpm.nonce,
                        comment: vm.comment,
                        project_id: vm.$route.params.projectid,
                        user_name: fpm.currentUserInfo.display_name,
                        commentable_type: vm.type,
                    };

                    if (vm.type === 'list') {
                        data.commentable_id = vm.$route.params.listid;
                    } else if (vm.type === 'todo') {
                        data.commentable_id = vm.$route.params.todoid;
                    } else if (vm.type === 'message') {
                        data.commentable_id = vm.$route.params.messageid;
                    }

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    if ( resp.success ) {
                        vm.comments.push({
                            comment: vm.comment,
                            user_name: data.user_name,
                            ID: resp.data.comment.ID,
                            avatar_url: resp.data.comment.avatar_url
                        });
                        vm.comment = '';
                    } else {
                        vm.message = resp.data;
                    }
                });
            },

            showCommentEditForm: function( commentObject, cindex ) {
                var vm = this;

                vm.cloneObject = JSON.parse(JSON.stringify(commentObject));
                vm.editindex = cindex;
                vm.commentEditText = commentObject.comment;
            },

            cancelCommentEdit: function( index ) {
                var vm = this;
                vm.editindex = -1;
                vm.comments[index] = vm.cloneObject;
                vm.cloneObject = '';
            },

            updateComment: function( commentObj ) {

                var vm = this,
                    data;

                // if (!vm.comment.trim()) {
                //     return;
                // }

                    data = {
                        action : 'fpm-insert-comment',
                        nonce : fpm.nonce,
                        comment: vm.commentEditText,
                        project_id: vm.$route.params.projectid,
                        user_name: fpm.currentUserInfo.display_name,
                        commentable_type: vm.type,
                        comment_id: commentObj.ID
                    };

                    if (vm.type === 'list') {
                        data.commentable_id = vm.$route.params.listid;
                    } else if (vm.type === 'todo') {
                        data.commentable_id = vm.$route.params.todoid;
                    } else if (vm.type === 'message') {
                        data.commentable_id = vm.$route.params.messageid;
                    }

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    // console.log(resp);
                    if ( resp.success ) {
                        commentObj.comment = vm.commentEditText;
                        vm.commentEditText = '';
                        // vm.attachments = [];
                        vm.editindex = -1;
                    } else {
                        vm.message = resp.data;
                    }
                });
            },

            deleteComment: function(comment, cindex) {

                if (confirm("Are you sure, you want to delete this comment ??")) {
                    var vm = this,
                        todoInfo,
                        data = {
                            action : 'fpm-delete-comment',
                            nonce : fpm.nonce,
                            comment_id: comment.ID
                        };

                    jQuery.post( fpm.ajaxurl, data, function( resp ) {
                        if ( resp.success ) {

                            vm.comments.splice( cindex, 1 );

                        } else {

                        }
                    });
                }
            },
        },

        created() {
            // var vm = this;
        },

        mounted() {
            console.log('comments component mounted.')
        }
    }
</script>