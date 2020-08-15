<template>
    <div>
        <li class="action-item" v-if="!item.is_bookmarked" @click="bookmarkItem( item )">
            <i class="fa fa-bookmark-o animated jello"></i>
            Bookmark
        </li>
        <li class="action-item" v-if="item.is_bookmarked" @click="removeBookmark( item )">
            <i class="fa fa-bookmark animated bounceIn"></i>
            Bookmarked
        </li>
    </div>
</template>

<script>
    export default{
        props: ['item', 'type'],
        methods: {
            bookmarkItem() {
                var vm = this,
                    data;
                data = {
                    action : 'fpm-bookmark-item',
                    nonce : fpm.nonce,
                    project_id: vm.item.projectID ? vm.item.projectID : vm.item.ID,
                    // user_id: item.userID, // backend theke current userid nia nite hbe
                    bookmark_id: vm.item.ID,
                    bookmark_type: vm.type,
                };
                if (vm.type === 'project') {
                    data.bookmark_title = vm.item.project_title;
                } else if (vm.type === 'todo') { //parentide
                    data.bookmark_title = vm.item.todo;
                    data.parent_id = vm.$route.params.listid;
                } else if (vm.type === 'list') {
                    data.bookmark_title = vm.item.list_title;
                } else if (vm.type === 'message') {
                    data.bookmark_title = vm.item.message_title;
                } else if (vm.type === 'folder') {
                    data.bookmark_title = vm.item.folder_title;
                } else if (vm.type === 'file') { //parentide
                    data.bookmark_title = vm.item.title +'.'+ vm.item.extension;
                    data.parent_id = vm.$route.params.folderid;
                    data.project_id = vm.$route.params.projectid;
                } else if (vm.type === 'event') {
                    data.bookmark_title = vm.item.event_title;
                }

                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    if ( resp.success ) {
                        vm.item.bookmark_id = resp.data.bookmark.ID
                        vm.item.is_bookmarked = true;
                        Event.$emit('toggle-actions');
                        Event.$emit('flash', {
                            message: resp.data.message,
                            flashtype:'success',
                            iconfont: 'fa-check'
                        });
                    }
                });
            },
            removeBookmark() {
                var vm = this,
                    data;

                data = {
                    action : 'fpm-remove-bookmark',
                    nonce : fpm.nonce,
                    bookmark_id: vm.item.bookmark_id
                };
                jQuery.post( fpm.ajaxurl, data, function( resp ) {
                    if ( resp.success ) {
                        Event.$emit('toggle-actions');
                        vm.item.is_bookmarked = false;
                        Event.$emit('flash', {
                            message: resp.data.message,
                            flashtype:'success',
                            iconfont: 'fa-check'
                        });

                    }
                });
            }
        },
    }
</script>
