const errorCollection = {
  HTTP: {
    500: (error) => {
      return Promise.reject(
        `${error.response.status} ${error.response.statusText}`
      );
    },

    413: (error) => {
      return Promise.reject(
        `${error.response.status} ${error.response.statusText}`
      );
    },

    405: (error) => {
      return Promise.reject({
        error: `${error.response.status} ${error.response.statusText}`,
        code: 405,
      });
    },

    404: (error) => {
      return Promise.reject({
        error: `${error.response.status} ${error.response.statusText}`,
        code: 404,
      });
    },

    403: (error) => {
      return Promise.reject(
        `${error.response.status} ${error.response.statusText}`
      );
    },

    401: (error) => Promise.reject(error),
  },

  custom: {
    unhandledStatusCode: () => Promise.reject("Unhandled response status code"),

    0: () => {
      return Promise.reject("Bad response from server");
    },

    102: ({ error }) =>
      Promise.reject({
        error,
        code: 102,
      }),
  },
};

export const responseManage = (response) => {
  if (typeof response.data == "string") return Promise.resolve(response);
  if ("error" in response.data) return errorManage(response);
  if (Array.isArray(response.data) && !response.data.length) return [];

  if ("status" in response.data) {
    if (response.data.status in errorCollection.custom)
      return errorCollection.custom[response.data.status]();

    if (response.data.status === 1) return Promise.resolve(response);
  }

  return Promise.resolve(response);
};

export const errorManage = (error) => {
  if ("response" in error) {
    if (error.response.status in errorCollection.HTTP)
      return errorCollection.HTTP[error.response.status](error);
  }

  if (error.data.error) {
    const { data } = error;

    if (data.status in errorCollection.custom)
      return errorCollection.custom[data.status](data);
    else return Promise.reject(error.data);
  }

  Promise.reject(error);
};
