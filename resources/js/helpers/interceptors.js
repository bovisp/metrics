import axios from 'axios'
import store from '../store'

axios.interceptors.response.use(
	response => response,
	error => {
		if (error.response.status === 422) {
			store.dispatch('setErrors', error.response.data.errors)
		}

		if (error.response.status === 403) {
			console.log(error.response.data.errors.errors)
			store.dispatch('setErrors', error.response.data.errors.errors)
		}

		if (error.response.status === 500) {
			store.dispatch('setErrors', {
				'internal_server_error': [
					'Internal server error'
				]
			})
		}

		return Promise.reject(error)
	}
)

axios.interceptors.request.use(
	config =>  {
		store.dispatch('clearErrors')

		return config
	}, 

	error =>  {
		return Promise.reject(error)
	}
);