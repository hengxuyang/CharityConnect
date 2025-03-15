module.exports = (sequelize, DataTypes) => {
  const Category = sequelize.define('Category', {
    id: {
      type: DataTypes.INTEGER,
      autoIncrement: true,
      primaryKey: true
    },
    name: {
      type: DataTypes.STRING,
      allowNull: false,
      unique: true
    }
  }, {
    timestamps: false,
    tableName: 'categories'
  });

  Category.associate = (models) => {
    Category.hasMany(models.Item, {
      foreignKey: 'category_id',
      as: 'items'
    });
  };

  return Category;
};
