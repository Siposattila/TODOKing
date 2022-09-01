<template>
	<div>
		<img alt="Vue logo" src="./assets/logo.png">
		<TodoTable :columns="['id', 'title', 'content', 'updatedAt', 'createdAt']" :todos="todos"/>
	</div>
</template>

<script>
	import { RepositoryFactory } from "@/repositories/RepositoryFactory";
	import TodoTable from "@/components/TodoTable.vue";

	const TodoRepository = RepositoryFactory.get("todos");

	export default {
		name: "App",
		components: {
			TodoTable
		},
		created() {
			this.getTodos();
		},
		data() {
			return {
				todos: []
			}
		},
		methods: {
			getTodos() {
				return TodoRepository.getTodos().then((response) => {
					console.log(response.data);
					this.todos = response.data;
				}).catch((error) => {
					console.error(error);
				});
			}
		}
	}
</script>

<style>
	#app {
		font-family: Avenir, Helvetica, Arial, sans-serif;
		-webkit-font-smoothing: antialiased;
		-moz-osx-font-smoothing: grayscale;
		text-align: center;
		color: #2c3e50;
		margin-top: 60px;
	}
	table {
		font-family: arial, sans-serif;
		border-collapse: collapse;
		width: 100%;
	}

	td, th {
		border: 1px solid #dddddd;
		text-align: left;
		padding: 8px;
	}

	tr:nth-child(even) {
		background-color: #dddddd;
	}
</style>
