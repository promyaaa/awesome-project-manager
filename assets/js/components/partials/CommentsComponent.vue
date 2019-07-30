<template>
    <div class="comment-content">
        <h3 class="decorated"><span>{{ i18n.comment_label }}</span></h3>

        <div v-for="(commentObject, cindex) in comments" class="comment-item">
            <div v-if="editindex !== cindex" class="comment">
                <div class="comment-avatar">
                    <img :src="commentObject.avatar_url" alt="">
                </div>
                <div class="comment-data">
                    <div v-html="commentObject.comment" class="comment-body"></div>
                    <div class="commented-by">
                        -- {{ i18n.comment_by}} {{commentObject.user_name}}
                    </div>
                    <div class="comment-action" v-if="currentUserInfo.roles[0] === 'administrator' || currentUserInfo.data.ID === commentObject.userID">
                        <span style="cursor: pointer;" @click="showCommentEditForm(commentObject, cindex)">
                            <!-- <a>{{i18n.edit}}</a> | -->
                            <a><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a> |
                        </span>
                        <span style="cursor: pointer;" @click="deleteComment(commentObject, cindex)">
                            <!-- <a>{{ i18n.delete }}</a> -->
                            <a><i class="fa fa-trash" aria-hidden="true"></i></a>
                        </span>
                    </div>
                </div>
            </div>

            <!-- edit section -->
            <div v-if="editindex === cindex" class="comment-form">
                <div class="current-user-avatar">
                    <img :src="currentUserInfo.data.avatar_url" :alt="currentUserInfo.data.display_name" width="50px" height="50px">
                </div>
                <div class="add_form_style">
                    <vue-editor id="edit-comment" v-model="commentEditText" :editorToolbar="customToolbar"></vue-editor>
                    <br>
                    <button class="button button-primary"
                        @click.prevent="updateComment(commentObject)"
                        :disabled="updatingComment"
                        >
                        <i v-if="updatingComment" class="fa fa-refresh fa-spin"></i>
                        {{ i18n.update }}
                    </button>
                    <button class="button button-default" @click="cancelCommentEdit(cindex)">{{ i18n.cancel }}</button>
                </div>
            </div>
        </div>

        <div style="margin-top: 15px;" class="comment-form">
            <div class="current-user-avatar">
                <img :src="currentUserInfo.data.avatar_url" :alt="currentUserInfo.data.display_name" width="50px" height="50px">
            </div>
            <div class="add_form_style">
                <vue-editor id="add-comment" v-model="comment" :editorToolbar="customToolbar"></vue-editor>
                <br>

                <div class="action">
                    <button class="button button-primary"
                        @click.prevent="addComment()"
                        :disabled="commenting"
                        >
                        <i v-if="commenting" class="fa fa-refresh fa-spin"></i>
                        {{ i18n.add_comment }}
                    </button>
                </div>
            </div>
            <div class="pm-clearfix"></div>
        </div>

    </div>
</template>

<script>
    import store from '../../store';
    import { VueEditor } from 'vue2-editor'
    export default {
        props: ['comments', 'type'],
        components: {
            VueEditor
        },
        data() {
            return {
                i18n: {},
                currentUserInfo:{},
                cloneObject: '',
                loading: false,
                commenting: false,
                updatingComment: false,
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
                vm.commenting = true;

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    if ( resp.success ) {
                        vm.commenting = false;
                        vm.comments.push({
                            comment: vm.comment,
                            user_name: vm.currentUserInfo.data.display_name,
                            userID: vm.currentUserInfo.data.ID,
                            ID: resp.data.comment.ID,
                            avatar_url: resp.data.comment.avatar_url
                        });
                        vm.comment = '';
                    } else {
                        vm.commenting = false;
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
                        user_name: fpm.currentUserInfo.data.display_name,
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
                
                vm.updatingComment = true;    

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    
                    if ( resp.success ) {
                        vm.updatingComment = false;
                        commentObj.comment = vm.commentEditText;
                        vm.commentEditText = '';
                        // vm.attachments = [];
                        vm.editindex = -1;
                    } else {
                        vm.updatingComment = false;
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
            var vm = this;
            this.currentUserInfo = fpm.currentUserInfo;

            store.setLocalization( 'fpm-get-comments-local-data' ).then( function( data ) {
                console.log(data);
                vm.i18n = data;
            });
        }
    }
</script>

<style>
    .comment-action .fa {
        color: #b5b5b5;
    }
    .comment-content {
        padding: 20px 32px;
    }

    .comment-content h3{
        margin-bottom: 30px;
    }

    .comment-content .comment-form .current-user-avatar {
        width: 7%;
        height: 50px;
        float: left;
        margin-right: 10px;
    }

    .comment-content .comment-form .current-user-avatar img{
        border-radius: 50px;
    }
    .comment-content .comment-form .add_form_style {
        width: 85.667%;
        float: left;
        padding: 15px 15px 25px;
    }

    .comment-item{
        position: relative;
        margin-bottom: 20px;
        overflow: hidden;
    }


    .comment-item .comment-action {
        position: absolute;
        top: 10px;
        right: 15px;
        font-size: 14px;
        visibility: hidden;
    }

    .comment-item:hover .comment-action{
        visibility: visible;
    }

    .comment-item .comment .comment-avatar {
        width: 7%;
        height: 50px;
        float: left;
        margin-right: 10px;
    }

    .comment-item .comment .comment-avatar img{
        border-radius: 50px;
    }

    .comment-item .comment .comment-data {
        width: 90%;
        float: left;
        background: #fafafa;
        padding: 5px 15px 25px;
        box-sizing: border-box;
        border:1px solid #f1f1f1;
        position: relative;
    }
    .comment-item .comment .comment-data .commented-by {
        position: absolute;
        bottom: 10px;
        right: 15px;
        font-style: italic;
        color: #c1c1c1;
        font-size: 14px;
    }
</style>