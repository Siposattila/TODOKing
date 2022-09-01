<template>
    <div>
        <table>
            <thead>
                <tr>
                    <th v-for="(column, index) in columns" :key="index"> {{column}}</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(todo, index) in todos" :key="index">
                    <td v-for="(column, indexColumn) in columns" :key="indexColumn">{{todo[column]}}</td>
                </tr>
            </tbody>
        </table>
        <div class="forms">
            <TodoForm :title="'Create'" :subtitle="'create a todo'" :submitTitle="'create'" :callback="'create'" :inputs="[
                {
                    id: 'title',
                    type: 'text',
                    placeholder: 'Title'
                },
                {
                    id: 'content',
                    type: 'text',
                    placeholder: 'Content'
                },
                {
                    id: 'attachment',
                    type: 'text',
                    placeholder: 'Attachment (data uri)'
                },
            ]" />
            <TodoForm :title="'Update'" :subtitle="'update a todo'" :submitTitle="'update'" :callback="'update'" :inputs="[
                {
                    id: 'id',
                    type: 'number',
                    placeholder: 'Id'
                },
                {
                    id: 'title',
                    type: 'text',
                    placeholder: 'Title'
                },
                {
                    id: 'content',
                    type: 'text',
                    placeholder: 'Content'
                },
                {
                    id: 'attachment',
                    type: 'text',
                    placeholder: 'Attachment (data uri)'
                },
            ]" />
            <TodoForm :title="'Delete'" :subtitle="'delete a todo'" :submitTitle="'delete'" :callback="'delete'" :inputs="[
                {
                    id: 'id',
                    type: 'number',
                    placeholder: 'Id'
                }
            ]" />
        </div>
    </div>
</template>

<script>
    import { RepositoryFactory } from "@/repositories/RepositoryFactory";
    import TodoForm from "@/components/TodoForm.vue";

    const TodoRepository = RepositoryFactory.get("todos");

    export default {
        name: "TodoTable",
        components: {
            TodoForm,
        },
        props: {
            todos: Array,
            columns: Array
        },
        data() {
            return {
                id: null,
                title: "",
                content: "",
                attachment: null
            }
        },
        methods: {
            create() {
                console.log(JSON.stringify({
                    title: this.title,
                    content: this.content,
                    attachment: this.attachment
                }));
                return TodoRepository.createTodo(JSON.stringify({
                    title: this.title,
                    content: this.content,
                    attachment: this.attachment
                })).then((response) => {
					console.log(response.data);
                    this.handleResponse(response.status);
				}).catch((error) => {
					console.error(error);
				});
            },
			update() {
				return TodoRepository.updateTodo(this.id, JSON.stringify({
                    title: this.title,
                    content: this.content,
                    attachment: this.attachment
                })).then((response) => {
					console.log(response.data);
                    this.handleResponse(response.status);
				}).catch((error) => {
					console.error(error);
				});
			},
            delete() {
                return TodoRepository.deleteTodo(this.id).then((response) => {
					console.log(response.data);
                    this.handleResponse(response.status);
				}).catch((error) => {
					console.error(error);
				});
            },
            handleResponse(status) {
                if (status === 200) {
                    this.$parent["getTodos"]();
                }
            }
		}
    }
</script>

<style>
    .forms {
        display: flex;
        margin-top: 50px;
    }
</style>
