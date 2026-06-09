import {  Package, Tag, CreditCard, Settings, GraduationCap, Award, Heart, Gift, Scissors , Calendar, Kanban } from 'lucide-react';

declare global {
    function route(name: string): string;
}

export const beautyspamanagementCompanyMenu = (t: (key: string) => string) => [
    {
        title: t('Beauty Spa Dashboard'),
        href: route('beauty-spa-management.index'),
        permission: 'manage-beauty-spa-dashboard',
        parent: 'dashboard',
        order: 170,
    },
    {
        title: t('Beauty Spa'),
        icon: Scissors,
        permission: 'manage-beauty-spa-management',
        href:'',
        order: 695,

        children: [
            {
                title: t('Bookings'),
                href: route('beauty-spa-management.beauty-bookings.index'),
                permission: 'manage-beauty-bookings',
            },
            {
                title: t('Payments'),
                href: route('beauty-spa-management.beauty-bookings.payments.index'),
                permission: 'manage-beauty-bookings-payment',
            },
            {
                title: t('Booking Order'),
                href: route('beauty-spa-management.booking-order.index'),
                permission: 'manage-beauty-bookings',
            },
            {
                title: t('Beauty Receipt'),
                href: route('beauty-spa-management.beauty-receipt.index'),
                permission: 'manage-beauty-receipt',
            },
            {
                title: t('Loyalty Programs'),
                href: route('beauty-spa-management.beauty-loyalty-programs.index'),
                permission: 'manage-beauty-loyalty-programs',
            },
            {
                title: t('Services'),
                href: route('beauty-spa-management.services.index'),
                permission: 'manage-beauty-services',
            },
            {
                title: t('Service Offers'),
                href: route('beauty-spa-management.beauty-service-offers.index'),
                permission: 'manage-beauty-service-offers',
            },
            {
                title: t('Memberships'),
                href: route('beauty-spa-management.beauty-memberships.index'),
                permission: 'manage-beauty-memberships',
            },
            {
                title: t('Trainings'),
                href: route('beauty-spa-management.trainings.index'),
                permission: 'manage-beauty-trainings',
            },
            {
                title: t('Certifications'),
                href: route('beauty-spa-management.certifications.index'),
                permission: 'manage-beauty-certifications',
            },
            {
                title: t('Gift Card'),
                href: route('beauty-spa-management.gift-cards.index'),
                permission: 'manage-beauty-gift-cards',
            },
            {
                title: t('Subscribers'),
                href: route('beauty-spa-management.beauty-subscribers.index'),
                permission: 'manage-beauty-subscribers',
            },
            {
                title: t('Contacts'),
                href: route('beauty-spa-management.beauty-contacts.index'),
                permission: 'manage-beauty-contacts',
            },
            {
                title: t('Reviews'),
                href: route('beauty-spa-management.beauty-reviews.index'),
                permission: 'manage-beauty-reviews',
            },
            {
                title: t('System Setup'),
                href: route('beauty-spa-management.service-types.index'),
                permission: 'manage-beauty-service-types',
            },
        ]
    },
];