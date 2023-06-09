<script setup>
import { ref, onBeforeMount, watch } from 'vue'
import useLayout from '@/composables/useLayout'

const route = useRoute()
const { layoutConfig, layoutState, setActiveMenuItem, onMenuToggle } =
	useLayout()
const props = defineProps({
	item: {
		type: Object,
		default: () => ({}),
	},
	index: {
		type: Number,
		default: 0,
	},
	root: {
		type: Boolean,
		default: true,
	},
	parentItemKey: {
		type: String,
		default: null,
	},
})
const isActiveMenu = ref(false)
const itemKey = ref(null)

onBeforeMount(() => {
	itemKey.value = props.parentItemKey
		? `${props.parentItemKey}-${props.index}`
		: String(props.index)
	const activeItem = layoutState.activeMenuItem

	isActiveMenu.value =
		activeItem === itemKey.value || activeItem
			? activeItem.startsWith(`${itemKey.value}-`)
			: false
})
watch(
	() => layoutConfig.activeMenuItem.value,
	newVal => {
		isActiveMenu.value =
			newVal === itemKey.value || newVal.startsWith(`${itemKey.value}-`)
	}
)

const itemClick = (event, item) => {
	if (item.disabled) {
		event.preventDefault()

		return
	}

	const { overlayMenuActive, staticMenuMobileActive } = layoutState

	if (
		(item.to || item.url) &&
		(staticMenuMobileActive.value || overlayMenuActive.value)
	) {
		onMenuToggle()
	}

	if (item.command) {
		item.command({ originalEvent: event, item })
	}

	const foundItemKey = item.items
		? isActiveMenu.value
			? props.parentItemKey
			: itemKey
		: itemKey.value

	setActiveMenuItem(foundItemKey)
}

const checkActiveRoute = item => route.path === item.to
</script>

<template>
	<li
		:class="{ 'layout-root-menuitem': root, 'active-menuitem': isActiveMenu }"
	>
		<div
			v-if="root && item.visible !== false"
			class="layout-menuitem-root-text text-gray-200 pl-2"
		>
			{{ item.label }}
		</div>

		<a
			v-if="(!item.to || item.items) && item.visible !== false"
			:href="item.url"
			:class="item.class"
			:target="item.target"
			tabindex="0"
			@click="itemClick($event, item, index)"
		>
			<i :class="item.icon" class="md:mr-2" />
			<span class="layout-menuitem-text hidden md:block">
				{{ item.label }}
			</span>
			<i
				v-if="item.items"
				class="pi pi-fw pi-angle-down layout-submenu-toggler"
			/>
		</a>

		<router-link
			v-if="item.to && !item.items && item.visible !== false"
			class="text-gray-200"
			:class="[item.class, { 'active-route': checkActiveRoute(item) }]"
			tabindex="0"
			:to="item.to"
			@click="itemClick($event, item, index)"
		>
			<i :class="item.icon" class="md:mr-2" />
			<span class="layout-menuitem-text text-grey-600 hidden md:block">
				{{ item.label }}
			</span>
			<i
				v-if="item.items"
				class="pi pi-fw pi-angle-down layout-submenu-toggler"
			/>
		</router-link>
		<Transition
			v-if="item.items && item.visible !== false"
			name="layout-submenu"
		>
			<ul v-show="root ? true : isActiveMenu" class="layout-submenu">
				<AppMenuItem
					v-for="(child, i) in item.items"
					:key="child"
					:index="i"
					:item="child"
					:parentItemKey="itemKey"
					:root="false"
				/>
			</ul>
		</Transition>
	</li>
</template>

<style lang="scss" scoped></style>
