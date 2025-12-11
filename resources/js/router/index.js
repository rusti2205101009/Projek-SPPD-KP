import { createRouter, createWebHistory } from 'vue-router';

import Login from '../pages/Login.vue';
import AdminLayout from '../layouts/AdminLayout.vue';
import UserLayout from '../layouts/UserLayout.vue';

// admin side
import AdminDashboard from '../pages/admin/AdminDashboard.vue';
import UserIndex from '../pages/admin/user/UserIndex.vue';
import UserCreate from '../pages/admin/user/UserCreate.vue';
import UserEdit from '../pages/admin/user/UserEdit.vue';

import ProfileIndex from '../pages/admin/profile/ProfileIndex.vue';
import ProfileEdit from '../pages/admin/profile/ProfileEdit.vue';

import EmployeeIndex from '../pages/admin/employee/EmployeeIndex.vue';
import EmployeeCreate from '../pages/admin/employee/EmployeeCreate.vue';
import EmployeeEdit from '../pages/admin/employee/EmployeeEdit.vue';

import KepalaIndex from '../pages/admin/kepala/KepalaIndex.vue';
import KepalaCreate from '../pages/admin/kepala/KepalaCreate.vue';
import KepalaEdit from '../pages/admin/kepala/KepalaEdit.vue';

import SptIndex from '../pages/admin/spt/SptIndex.vue';
import SptCreate from '../pages/admin/spt/SptCreate.vue';
import SptEdit from '../pages/admin/spt/SptEdit.vue';

import SppdIndex from '../pages/admin/sppd/SppdIndex.vue';
import SppdEdit from '../pages/admin/sppd/SppdEdit.vue';

import BiayaIndex from '../pages/admin/biaya/BiayaIndex.vue';
import BiayaEdit from '../pages/admin/biaya/BiayaEdit.vue';

import BerangkatIndex from '../pages/admin/berangkat/BerangkatIndex.vue';
import BerangkatEdit from '../pages/admin/berangkat/BerangkatEdit.vue';

import PulangIndex from '../pages/admin/pulang/PulangIndex.vue';
import PulangEdit from '../pages/admin/pulang/PulangEdit.vue';

import Template from '../pages/admin/template/Template.vue';

// user side
import UserDashboard from '../pages/user/UserDashboard.vue';
import UserProfileEdit from '../pages/user/UserProfileEdit.vue';
import PasswordEdit from '../pages/user/PasswordEdit.vue';

import Employee from '../pages/user/employee/EmployeeIndex.vue';

import SptIndexUser from '../pages/user/spt/SptIndex.vue';
import SptCreateUser from '../pages/user/spt/SptCreate.vue';
import SptEditUser from '../pages/user/spt/SptEdit.vue';

import SppdIndexUser from '../pages/user/sppd/SppdIndex.vue';
import SppdEditUser from '../pages/user/sppd/SppdEdit.vue';

import BiayaIndexUser from '../pages/user/biaya/BiayaIndex.vue';
import BiayaEditUser from '../pages/user/biaya/BiayaEdit.vue';

import BerangkatIndexUser from '../pages/user/berangkat/BerangkatIndex.vue';
import BerangkatEditUser from '../pages/user/berangkat/BerangkatEdit.vue';

import PulangIndexUser from '../pages/user/pulang/PulangIndex.vue';
import PulangEditUser from '../pages/user/pulang/PulangEdit.vue';

const getUser = () => {
  try {
    return JSON.parse(localStorage.getItem('user'))
  } catch {
    return null
  }
}
const getUserRole = () => getUser()?.role
const isLoggedIn = () => !!localStorage.getItem('token')

const routes = [
    {
        path: '/',
        redirect: () => {
            if (!isLoggedIn()) {
                return '/login';
            }
            const role = getUserRole();
            return role === 'admin' ? '/admin/dashboard' : '/user/dashboard';
        }
    },

    {
        path: '/login',
        name: 'Login',
        component: Login,
        meta: { guestOnly: true }
    },

    {
        path: '/admin',
        component: AdminLayout,
        meta: { requiresAuth: true, role: 'admin' },
        children: [
            {
                path: 'dashboard',
                name: 'AdminDashboard',
                component: AdminDashboard
            },
            {
                path: 'user',
                name: 'UserIndex',
                component: UserIndex
            },
            {
                path: 'user/create',
                name: 'UserCreate',
                component: UserCreate,
            },
            {
                path: 'user/edit/:id',
                name: 'UserEdit',
                component: UserEdit,
                props: true,
            },
            {
                path: 'pegawai',
                name: 'EmployeeIndex',
                component: EmployeeIndex
            },
            {
                path: 'pegawai/create',
                name: 'EmployeeCreate',
                component: EmployeeCreate,
            },
            {
                path: 'pegawai/edit/:id',
                name: 'EmployeeEdit',
                component: EmployeeEdit,
                props: true,
            },
            {
                path: 'user-profile',
                name: 'ProfileIndex',
                component: ProfileIndex
            },
            {
                path: 'user-profile/edit/:id',
                name: 'ProfileEdit',
                component: ProfileEdit,
                props: true,
            },
            {
                path: 'kepala',
                name: 'KepalaIndex',
                component: KepalaIndex
            },
            {
                path: 'kepala/create',
                name: 'KepalaCreate',
                component: KepalaCreate,
            },
            {
                path: 'kepala/edit/:id',
                name: 'KepalaEdit',
                component: KepalaEdit,
                props: true,
            },
            {
                path: 'spt',
                name: 'SptIndex',
                component: SptIndex
            },
            {
                path: 'spt/create',
                name: 'SptCreate',
                component: SptCreate,
            },
            {
                path: 'spt/edit/:id',
                name: 'SptEdit',
                component: SptEdit,
                props: true,
            },
            {
                path: 'sppd',
                name: 'SppdIndex',
                component: SppdIndex
            },
            {
                path: 'sppd/edit/:id',
                name: 'SppdEdit',
                component: SppdEdit,
                props: true,
            },
            {
                path: 'rincian-biaya',
                name: 'BiayaIndex',
                component: BiayaIndex
            },
            {
                path: 'rincian-biaya/edit/:id',
                name: 'BiayaEdit',
                component: BiayaEdit,
                props: true,
            },
            {
                path: 'keberangkatan',
                name: 'BerangkatIndex',
                component: BerangkatIndex
            },
            {
                path: 'keberangkatan/edit/:id',
                name: 'BerangkatEdit',
                component: BerangkatEdit,
                props: true,
            },
            {
                path: 'kepulangan',
                name: 'PulangIndex',
                component: PulangIndex
            },
            {
                path: 'kepulangan/edit/:id',
                name: 'PulangEdit',
                component: PulangEdit,
                props: true,
            },
            {
                path: 'template',
                name: 'Template',
                component: Template
            },
        ]
    },

    {
        path: '/user',
        component: UserLayout,
        meta: { requiresAuth: true, role: 'user' },
        children: [
            {
                path: 'dashboard',
                name: 'UserDashboard',
                component: UserDashboard
            },
            {
                path: 'user-profile/edit',
                name: 'UserProfileEdit',
                component: UserProfileEdit
            },
            {
                path: 'password/edit',
                name: 'PasswordEdit',
                component: PasswordEdit
            },
            {
                path: 'spt',
                name: 'SptIndexUser',
                component: SptIndexUser
            },
            {
                path: 'pegawai',
                name: 'Employee',
                component: Employee
            },
            {
                path: 'spt/create',
                name: 'SptCreateUser',
                component: SptCreateUser,
            },
            {
                path: 'spt/edit/:id',
                name: 'SptEditUser',
                component: SptEditUser,
                props: true,
            },
            {
                path: 'sppd',
                name: 'SppdIndexUser',
                component: SppdIndexUser
            },
            {
                path: 'sppd/edit/:id',
                name: 'SppdEditUser',
                component: SppdEditUser,
                props: true,
            },
            {
                path: 'rincian-biaya',
                name: 'BiayaIndexUser',
                component: BiayaIndexUser
            },
            {
                path: 'rincian-biaya/edit/:id',
                name: 'BiayaEditUser',
                component: BiayaEditUser,
                props: true,
            },
            {
                path: 'keberangkatan',
                name: 'BerangkatIndexUser',
                component: BerangkatIndexUser
            },
            {
                path: 'keberangkatan/edit/:id',
                name: 'BerangkatEditUser',
                component: BerangkatEditUser,
                props: true,
            },
            {
                path: 'kepulangan',
                name: 'PulangIndexUser',
                component: PulangIndexUser
            },
            {
                path: 'kepulangan/edit/:id',
                name: 'PulangEditUser',
                component: PulangEditUser,
                props: true,
            },
        ]
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('token');
    const user = getUser()
    const role = user?.role

    if (to.meta.requiresAuth && (!token || !user)) {
       return next({ name: 'Login' })
    } 

    // guest only (misal login page)
    if (to.meta.guestOnly && token) {
        return next(role === 'admin' ? '/admin/dashboard' : '/user/dashboard')
    }

    if (to.meta.role && to.meta.role !== role) {
        return next({ name: 'Login' })
    }
    next()
})

export default router;