import Vue from './vue';
import router from './routes';
import store from './store';
import FlashMessage from './components/partials/FlashMessageComponent.vue';

window.wpmedia = wp.media;


// Tooltip directive
Vue.directive('tooltip', {
    bind: function( el, binding, vnode ) {
        jQuery(el).tooltip('show');
    },
    unbind: function( el, binding, vnode ) {
        jQuery(el).tooltip('destroy');
    }
});

window.Event = new Vue();
/**
 * As we are using hash based navigation, hack fix
 * to highlight the current selected menu
 *
 * Requires jQuery
 */
function menuFix(slug) {
    var $ = jQuery;

    let menuRoot = $('#toplevel_page_' + slug);
    let currentUrl = window.location.href;
    let currentPath = currentUrl.substr( currentUrl.indexOf('admin.php') );

    menuRoot.on('click', 'a', function() {
        var self = $(this);

        $('ul.wp-submenu li', menuRoot).removeClass('current');

        if ( self.hasClass('wp-has-submenu') ) {
            $('li.wp-first-item', menuRoot).addClass('current');
        } else {
            self.parents('li').addClass('current');
        }
    });

    $('ul.wp-submenu a', menuRoot).each(function(index, el) {
        if ( $(el).attr( 'href' ) === currentPath ) {
            $(el).parent().addClass('current');
            return;
        }
    });
}

export default menuFix;


new Vue({

    el: '#app',
    router: router,
    components: {
        FlashMessage
    },
    data: {
        store: store
    },

    // watch: {
    //     '$route' (to, from) {
    //         console.log(to);
    //     }
    // },

    methods: {
        getLocalizeString: function() {
            var vm = this,
                todo,
                data = {
                    action : 'fpm-get-local-data'
                };

            jQuery.post( fpm.ajaxurl, data, function( resp ) {

                if ( resp.success ) {
                    vm.store.setLocalizeData(resp.data);
                } else {

                }
            });
        },
    },

    created: function() {
        // this.getLocalizeString();
        // jQuery(document).on('heartbeat-tick', function(e, data) {
        //      Math.floor(Math.random() * 6) * 1000
        //     console.log('test heartbeat-send');
        // });
    }
});


menuFix('fusion-pm');
// Vue.directive('focus', {
//     inserted: function (el) {
//     // Focus the element
//         el.focus()
//     }
// });

// EXAMPLE DIRECTIVE
// <template>
//    <input type="text" v-focus>
// </template>

// <script>
//  const focus = {
//     inserted(el) {
//       el.focus()
//     },
//   }

//   export default {
//     directives: { focus },
//     // ...
//   }
// </script>