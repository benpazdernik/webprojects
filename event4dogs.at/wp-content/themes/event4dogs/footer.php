			<footer class="footer" role="contentinfo">

				<div id="inner-footer" class="wrap clearfix">

					<nav role="navigation">
							<?php bones_footer_links(); ?>
					</nav>

					<p class="source-org copyright">&copy; <?php echo date('Y'); ?> <?php bloginfo( 'name' ); ?>.</p>

				</div>

				<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer Widget')) : endif; ?>

			</footer>

		</div>

		<?php // all js scripts are loaded in library/bones.php ?>
		<?php wp_footer(); ?>

	</body>

</html>