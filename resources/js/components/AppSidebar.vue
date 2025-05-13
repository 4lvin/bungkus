<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BriefcaseBusiness, LayoutGrid, LayoutList, ScrollText } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const currentPath = window.location.pathname;
const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: route('dashboard'),
        icon: LayoutGrid,
        isActive: currentPath === new URL(route('dashboard')).pathname,
    },
    {
        title: 'Kategori',
        href: route('admin.categories.index'),
        icon: BriefcaseBusiness,
        isActive: currentPath === new URL(route('admin.categories.index')).pathname,
    },
    {
        title: 'Produk',
        href: route('admin.products.index'),
        icon: LayoutList,
        isActive: currentPath === new URL(route('admin.products.index')).pathname,
    },
    {
        title: 'Order',
        href: route('dashboard'),
        icon: ScrollText,
        isActive: currentPath === new URL(route('dashboard')).pathname,
    },
];

const footerNavItems: NavItem[] = [];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
