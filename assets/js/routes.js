import Vue from './vue';
import VueRouter from './vue-router';

Vue.use(VueRouter);

const Todo = require('./components/Todo.vue');
const Home = require('./components/Home.vue');
const Summary = require('./components/Summary.vue');
const EditProject = require('./components/partials/EditProject.vue');
const ProjectActivities = require('./components/ProjectActivities.vue');


const TodoLists = require('./components/TodoLists.vue');
const TodoList = require('./components/TodoList.vue');
const Users = require('./components/Users.vue');

const Activities = require('./components/Activities.vue');
const ProjectStatus = require('./components/ProjectStatus.vue');

const Messages = require('./components/Messages.vue');
const Message = require('./components/Message.vue');
const NewMessage = require('./components/partials/NewMessage.vue');
const EditMessage = require('./components/partials/EditMessage.vue');

const MyAssignments = require('./components/MyAssignments.vue');
const MyActivity = require('./components/MyActivity.vue');

const routes = [
    { path: '/', component: Home },

    { path: '/my/assignments', component: MyAssignments },

    { path: '/my/activity', component: MyActivity },

    { path: '/projects', component: Home },

    { path: '/projects/:projectid', component: Summary },

    { path: '/projects/:projectid/reports', component: ProjectActivities },


    // { path: '/projects/:projectid/activities', component: Activities },

    { path: '/projects/:projectid/edit', component: EditProject },

    { path: '/projects/:projectid/status', component: ProjectStatus },

    { path: '/projects/:projectid/todolists', component: TodoLists },

    { path: '/projects/:projectid/todolists/:listid', component: TodoList },

    { path: '/projects/:projectid/todolists/:listid/todos', component: TodoList },

    { path: '/projects/:projectid/todolists/:listid/todos/:todoid', component: Todo },

    { path: '/projects/:projectid/users', component: Users },

    { path: '/projects/:projectid/messages', component: Messages },

    { path: '/projects/:projectid/messages/new', component: NewMessage },

    { path: '/projects/:projectid/messages/:messageid', component: Message },

    { path: '/projects/:projectid/messages/:messageid/edit', component: EditMessage },

    { path: '*', redirect: '/' }
];

export default new VueRouter({
    // mode: 'history',
    routes,
    linkActiveClass: 'is-active'
});