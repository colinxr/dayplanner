import { ref } from 'vue'
import { defineStore } from 'pinia'
import { ClientI, SubmissionI } from './types'

export default defineStore('dashboardStore', () => {
	const submissions = ref<SubmissionI[]>([])
	const activeSubmission = ref<SubmissionI>()
	const nextPage = ref<number | null>(1)

	// const getNextPageFromUrl = (url: string | null): number | null => {
	// if (!url) return null

	// const match = url.match(/page=(\d+)/)

	// return match ? Number(match[1]) : null
	// }

	const setActiveSubmission = (submission: SubmissionI) => {
		activeSubmission.value = submission
	}

	const findSubmissionById = (activeSubId: number) =>
		submissions.value.find(({ id }) => id === activeSubId)

	const getSubmissions = () => {
		if (!nextPage.value) return

		try {
			// // const { data } = await ApiService.submissions.index(nextPage.value)
			// submissions.value = [...submissions.value, ...data.submissions.data]
			// nextPage.value = getNextPageFromUrl(data.submissions.next_page_url)
		} catch (error) {
			console.log(error)
		}
	}

	const updateSubmissionClient = (client: ClientI) => {
		if (!activeSubmission.value) return

		const submission = findSubmissionById(activeSubmission.value.id)

		if (!submission) return

		submission.client = client

		submissions.value = submissions.value.map(sub => {
			if (sub.id === submission.id) return sub

			return sub
		})

		activeSubmission.value.client = client
	}

	return {
		submissions,
		nextPage,
		activeSubmission,
		getSubmissions,
		setActiveSubmission,
		updateSubmissionClient,
		findSubmissionById,
	}
})
