const Login = () => import(/* webpackChunkName: "customer" */'../views/auth/Login')
const Register = () => import(/* webpackChunkName: "customer" */'../views/auth/Register')
const EmailConfirmation = () => import(/* webpackChunkName: "customer" */'../views/auth/EmailConfirmation')
const OrganizationInformation = () => import(/* webpackChunkName: "customer" */'../views/setup/OrganizationInformation.vue')
const Services = () => import(/* webpackChunkName: "customer" */'../views/setup/Services.vue')
export default [
    {
        path: 'login',
        name: 'Đăng nhập',
        component: Login,
        meta: {
            auth: false
        }
    },
    {
        path: 'register',
        name: 'Đăng kí',
        component: Register,
        meta: {
            auth: false
        }
    },
    {
        path: 'email-confirmation',
        name: 'email-confirmation',
        component: EmailConfirmation,
        props: true
    },
    {
        path: 'organization-information',
        name: 'organization-information',
        component: OrganizationInformation,
        props: true
    },
    {
        path: 'services',
        name: 'services',
        component: Services,
        props: true
    }
]